<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Symfony\Component\HttpFoundation\Request;

/**
 * A payment provider with a hosted payment page. Card details never reach the site.
 */
interface ProviderInterface
{
    public function createCheckout(PaymentRequest $request): Checkout;

    /**
     * @return string PaymentStatus::PAID, PaymentStatus::PENDING or PaymentStatus::FAILED
     */
    public function fetchStatus(string $reference): string;

    /**
     * Refund the full payment
     */
    public function refund(string $reference, int $amount, string $currency): void;

    /**
     * The payment reference a webhook call is about, or null. The body is not trusted: the status is always
     * fetched from the provider's API afterwards.
     */
    public function getWebhookReference(Request $request): string|null;
}
