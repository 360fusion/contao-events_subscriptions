<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Stripe Checkout (https://docs.stripe.com/api/checkout/sessions). Test or live mode follows the secret key.
 */
class StripeProvider implements ProviderInterface
{
    private const API = 'https://api.stripe.com/v1';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $secretKey,
    ) {
    }

    public function createCheckout(PaymentRequest $request): Checkout
    {
        $session = $this->request('POST', '/checkout/sessions', [
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($request->currency),
                    'unit_amount' => $request->amount,
                    'product_data' => ['name' => $request->description],
                ],
            ]],
            'success_url' => $request->returnUrl,
            'cancel_url' => $request->returnUrl,
            'client_reference_id' => $request->bookingToken,
            'metadata' => ['booking' => $request->bookingToken],
        ] + ('' !== $request->email ? ['customer_email' => $request->email] : [])
            // Stripe allows 30 minutes to 24 hours: the payment page closes with the hold on the place (a minute later, to stay in range)
            + ($request->holdSeconds > 0 ? ['expires_at' => time() + min(max($request->holdSeconds + 60, 1860), 86340)] : []));

        return new Checkout($session['url'], $session['id']);
    }

    public function fetchStatus(string $reference): string
    {
        $session = $this->request('GET', '/checkout/sessions/'.rawurlencode($reference));

        return match (true) {
            'paid' === $session['payment_status'] => PaymentStatus::PAID,
            'expired' === $session['status'] => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };
    }

    public function refund(string $reference, int $amount, string $currency): void
    {
        $session = $this->request('GET', '/checkout/sessions/'.rawurlencode($reference));

        $this->request('POST', '/refunds', ['payment_intent' => $session['payment_intent'], 'amount' => $amount]);
    }

    public function getWebhookReference(Request $request): string|null
    {
        $event = json_decode($request->getContent(), true);
        $object = $event['data']['object'] ?? [];

        return 'checkout.session' === ($object['object'] ?? null) ? ($object['id'] ?? null) : null;
    }

    private function request(string $method, string $path, array $body = []): array
    {
        return $this->client->request($method, self::API.$path, [
            'auth_bearer' => $this->secretKey,
            'body' => $body ? http_build_query($body) : null,
            'headers' => $body ? ['Content-Type' => 'application/x-www-form-urlencoded'] : [],
        ])->toArray();
    }
}
