<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Codefog\EventsSubscriptionsBundle\Event\SubscribeEvent;
use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\EventDispatcher;
use Codefog\EventsSubscriptionsBundle\EventListener\NotificationListener;
use Codefog\EventsSubscriptionsBundle\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Model\PaymentModel;
use Codefog\EventsSubscriptionsBundle\Model\PaymentProviderModel;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Events;
use Contao\StringUtil;
use Contao\System;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Takes the payments of bookings: starts the checkout, confirms or cancels the booking, refunds
 */
class PaymentManager
{
    public function __construct(private readonly HttpClientInterface|null $client = null)
    {
    }

    /**
     * Decide how a new guest booking is paid. Returns the provider's checkout URL if the booker pays by card now.
     */
    public function handleNewSubscription(EventConfig $config, SubscriptionModel $model, string $chosenMethod, string $purchaseOrder, bool $onWaitingList, int $successPageId = 0): string|null
    {
        $methods = $config->getAvailablePaymentMethods();

        // Free event: nothing changes
        if (!$methods) {
            return null;
        }

        $status = PaymentDecision::decide($methods, $config->getFreeEmailDomains(), (string) $model->email, $chosenMethod, $onWaitingList, Features::confirmDomain());
        $model->paymentStatus = $status;

        if (\in_array($status, [PaymentStatus::FREE, PaymentStatus::CONFIRM], true)) {
            $model->save();

            return null;
        }

        $provider = $config->getPaymentProvider();
        $payment = new PaymentModel();
        $payment->tstamp = $payment->dateCreated = time();
        $payment->subscription = $model->id;
        $payment->event = $config->getEvent()->id;
        $payment->eventTitle = StringUtil::decodeEntities($config->getEvent()->title);
        $payment->name = trim($model->firstname.' '.$model->lastname);
        $payment->email = (string) $model->email;
        $payment->method = PaymentStatus::INVOICE === $status ? 'invoice' : 'card';
        $payment->status = $status;
        $payment->amount = $config->getPrice() * max(1, (int) $model->numberOfParticipants);
        $payment->currency = $provider?->currency ?: 'GBP';
        $payment->purchaseOrder = PaymentStatus::INVOICE === $status ? $purchaseOrder : '';
        $payment->save();

        $model->payment = $payment->id;
        $model->save();

        if (PaymentStatus::PENDING !== $status) {
            return null;
        }

        try {
            return $this->startCheckout($payment, $config, $model, $successPageId);
        } catch (\Throwable $e) {
            // Free the place again, the booker can try once more
            $this->fail($payment);

            throw $e;
        }
    }

    /**
     * Create the provider's checkout for a pending or unpaid payment
     */
    public function startCheckout(PaymentModel $payment, EventConfig $config, SubscriptionModel $model, int $successPageId = 0): string
    {
        if (null === ($providerModel = $config->getPaymentProvider())) {
            throw new \RuntimeException('The event has no payment provider');
        }

        $eventUrl = Events::generateEventUrl($config->getEvent(), true);
        $returnUrl = $eventUrl.(str_contains($eventUrl, '?') ? '&' : '?').http_build_query(['payment' => 'return', 'booking' => $model->unsubscribeToken] + ($successPageId ? ['next' => $successPageId] : []));

        // A new checkout replaces the reference: if the earlier one was paid, do not lose that payment
        if ($payment->reference && PaymentStatus::PAID === $this->sync($payment)) {
            return $returnUrl;
        }

        $checkout = $this->getProvider($providerModel)->createCheckout(new PaymentRequest(
            (int) $payment->amount,
            (string) $payment->currency,
            (string) $payment->eventTitle,
            (string) $model->unsubscribeToken,
            $returnUrl,
            WebhookUrl::generate((int) $providerModel->id),
            (string) $model->email,
            Features::paymentHoldMinutes() * 60,
        ));

        $payment->provider = $providerModel->id;
        $payment->reference = $checkout->reference;
        $payment->tstamp = time();
        $payment->save();

        return $checkout->url;
    }

    /**
     * Ask the provider for the status of a card payment and confirm or cancel the booking
     *
     * @return string The payment status afterwards
     */
    public function sync(PaymentModel $payment): string
    {
        if (!\in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::UNPAID, PaymentStatus::FAILED], true) || !$payment->reference || null === ($providerModel = PaymentProviderModel::findByPk($payment->provider))) {
            return (string) $payment->status;
        }

        $status = $this->getProvider($providerModel)->fetchStatus((string) $payment->reference);

        if (PaymentStatus::PAID === $status) {
            $this->markPaid($payment);

            return PaymentStatus::PAID;
        }

        if (PaymentStatus::FAILED === $status && PaymentStatus::PENDING === $payment->status) {
            $this->fail($payment);

            return PaymentStatus::FAILED;
        }

        return (string) $payment->status;
    }

    /**
     * Mark the payment as paid. Bookings that waited for the card payment get their confirmation e-mail now.
     */
    public function markPaid(PaymentModel $payment): void
    {
        $previous = $payment->status;

        // Only once, also when the return page and the webhook arrive at the same time
        $updated = Database::getInstance()
            ->prepare("UPDATE tl_calendar_events_payment SET status=?, paidAt=?, tstamp=? WHERE id=? AND status IN ('pending', 'unpaid', 'invoice', 'failed')")
            ->execute(PaymentStatus::PAID, time(), time(), $payment->id)
            ->affectedRows;

        if (!$updated) {
            return;
        }

        $payment->refresh();
        $this->log(sprintf('Payment ID %s of %s for "%s" was paid', $payment->id, $payment->name, $payment->eventTitle));

        // The booking was cancelled when the hold ran out: the money was taken, so someone must refund it or book the person in again
        if (PaymentStatus::FAILED === $previous) {
            $this->log(sprintf('Payment ID %s of %s for "%s" was paid after the booking expired: refund it in Events › Payments, or book the person in', $payment->id, $payment->name, $payment->eventTitle));
        }

        if (null === ($model = SubscriptionModel::findByPk($payment->subscription))) {
            return;
        }

        $model->paymentStatus = PaymentStatus::PAID;
        $model->save();

        if (PaymentStatus::PENDING === $previous) {
            $subscription = Services::getSubscriptionFactory()->createFromModel($model);
            Services::getEventDispatcher()->dispatch(EventDispatcher::EVENT_ON_SUBSCRIBE, new SubscribeEvent($model, $subscription));
        }
    }

    /**
     * Refund a paid payment in full (card payments through the provider)
     */
    public function refund(PaymentModel $payment): void
    {
        if (PaymentStatus::PAID !== $payment->status) {
            throw new \RuntimeException('Only paid payments can be refunded');
        }

        if ('card' === $payment->method) {
            if (null === ($providerModel = PaymentProviderModel::findByPk($payment->provider))) {
                throw new \RuntimeException('The payment provider of the payment no longer exists');
            }

            $this->getProvider($providerModel)->refund((string) $payment->reference, (int) $payment->amount, (string) $payment->currency);
        }

        $payment->status = PaymentStatus::REFUNDED;
        $payment->refundedAt = $payment->tstamp = time();
        $payment->save();

        if (null !== ($model = SubscriptionModel::findByPk($payment->subscription))) {
            $model->paymentStatus = PaymentStatus::REFUNDED;
            $model->save();
        }

        $this->log(sprintf('Payment ID %s of %s for "%s" was refunded', $payment->id, $payment->name, $payment->eventTitle));
    }

    /**
     * Cancel the bookings of card checkouts that were not completed in time, so their places are free again
     *
     * @return int The number of cancelled bookings
     */
    public function expirePending(int|null $maxAge = null): int
    {
        $maxAge ??= Features::paymentHoldMinutes() * 60;
        $expired = 0;

        foreach (PaymentModel::findBy(['status=?', 'dateCreated<?'], [PaymentStatus::PENDING, time() - $maxAge]) ?? [] as $payment) {
            try {
                // A webhook may have been missed
                if (PaymentStatus::PENDING !== $this->sync($payment)) {
                    continue;
                }
            } catch (\Throwable) {
                // The provider cannot be reached, the booking expires anyway
            }

            $this->fail($payment);
            ++$expired;
        }

        return $expired;
    }

    /**
     * Cancel the bookings of free e-mail domains whose address was not confirmed within a day, so their places are free again
     *
     * @return int The number of cancelled bookings
     */
    public function expireUnconfirmed(int $maxAge = 86400): int
    {
        $expired = 0;

        foreach (SubscriptionModel::findBy(['paymentStatus=?', 'dateCreated<?'], [PaymentStatus::CONFIRM, time() - $maxAge]) ?? [] as $model) {
            $this->release($model);
            ++$expired;
        }

        return $expired;
    }

    /**
     * Remove a booking that never became real and let the next people on the waiting list move up
     */
    private function release(SubscriptionModel $model): void
    {
        try {
            (new NotificationListener())->promoteFromWaitingList(new UnsubscribeEvent($model, Services::getSubscriptionFactory()->createFromModel($model)));
        } catch (\Throwable $e) {
            $this->log(sprintf('The waiting list could not be updated after the booking of %s expired: %s', trim($model->firstname.' '.$model->lastname), $e->getMessage()));
        }

        $model->delete();
    }

    /**
     * The payment failed or was abandoned: remove the waiting booking without notifications
     */
    private function fail(PaymentModel $payment): void
    {
        // Only a payment that is still pending: if it was paid a moment ago, the booking stays
        $updated = Database::getInstance()
            ->prepare("UPDATE tl_calendar_events_payment SET status=?, tstamp=? WHERE id=? AND status='pending'")
            ->execute(PaymentStatus::FAILED, time(), $payment->id)
            ->affectedRows;

        if (!$updated) {
            return;
        }

        $payment->refresh();

        if (null !== ($model = SubscriptionModel::findByPk($payment->subscription)) && PaymentStatus::PENDING === $model->paymentStatus) {
            // The place is free again: the next people on the waiting list move up (no cancellation e-mail to someone who never paid)
            $this->release($model);
        }
    }

    private function getProvider(PaymentProviderModel $model): ProviderInterface
    {
        return ProviderFactory::create($model->row(), $this->client);
    }

    private function log(string $message): void
    {
        System::getContainer()->get('monolog.logger.contao')->info($message, ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]);
    }
}
