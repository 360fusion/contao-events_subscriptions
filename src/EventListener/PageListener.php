<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Model\PaymentModel;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentManager;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\HasteBundle\UrlParser;
use Contao\Controller;
use Contao\CoreBundle\Event\LayoutEvent;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;

class PageListener
{
    /**
     * Pages with a "Twig layout with slots", for which Contao does not run the
     * generatePage hook.
     */
    public function onLayout(LayoutEvent $event): void
    {
        $this->onGeneratePage();
    }

    /**
     * On generate the page
     */
    public function onGeneratePage()
    {
        if ($token = Input::get('event_unsubscribe')) {
            $this->unsubscribe($token);
        }

        if (($action = Input::get('payment')) && ($token = Input::get('booking'))) {
            $this->handlePayment((string) $action, (string) $token);
        }
    }

    /**
     * The booker came back from the payment page ("return"), or wants to pay ("pay", link in the e-mails)
     */
    private function handlePayment(string $action, string $token): void
    {
        $cleanUrl = System::getContainer()->get(UrlParser::class)->removeQueryString(['payment', 'booking', 'next']);
        $model = SubscriptionModel::findOneBy('unsubscribeToken', $token);
        $payment = $model?->payment ? PaymentModel::findByPk($model->payment) : null;

        if (null === $payment) {
            Controller::redirect($cleanUrl);
        }

        $manager = new PaymentManager();
        $eventId = (int) $model->pid;

        if ('pay' === $action && \in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::UNPAID], true)) {
            throw new RedirectResponseException($manager->startCheckout($payment, Services::getEventConfigFactory()->create($eventId), $model), 303);
        }

        try {
            $status = $manager->sync($payment);
        } catch (\Throwable) {
            $status = PaymentStatus::PENDING; // the provider could not be reached, the webhook or the cron will catch up
        }

        if (PaymentStatus::PAID === $status && ($page = PageModel::findPublishedById((int) Input::get('next'))) !== null) {
            Controller::redirect($page->getFrontendUrl());
        }

        $message = $GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentMessage'][$status] ?? '';

        if (\in_array($status, [PaymentStatus::PENDING, PaymentStatus::UNPAID], true)) {
            $message = sprintf($message, StringUtil::specialcharsUrl($cleanUrl.(str_contains($cleanUrl, '?') ? '&' : '?').http_build_query(['payment' => 'pay', 'booking' => $token])));
        }

        Services::getFlashMessage()->set($message, $eventId);
        Controller::redirect($cleanUrl);
    }

    /**
     * Unsubscribe the user
     *
     * @param string $token
     */
    private function unsubscribe($token)
    {
        if (($subscriptionModel = SubscriptionModel::findOneBy('unsubscribeToken', $token)) !== null) {
            $config = Services::getEventConfigFactory()->create($subscriptionModel->pid);
            $calendar = $config->getCalendar();
            $subscription = Services::getSubscriptionFactory()->createFromModel($subscriptionModel);

            // Unsubscribe
            Services::getSubscriber()->unsubscribe($config, $subscription);

            // Redirect if we have a special page for that
            if ($calendar->subscription_unsubscribeLinkPage && ($page = PageModel::findPublishedById($calendar->subscription_unsubscribeLinkPage)) !== null) {
                Controller::redirect($page->getFrontendUrl());
            }
        }

        // Simply reload the page without token parameter
        Controller::redirect(System::getContainer()->get(UrlParser::class)->removeQueryString(['event_unsubscribe']));
    }
}
