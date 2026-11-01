<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * PayPal Orders v2 (https://developer.paypal.com/docs/api/orders/v2/). The order is captured when the payer returns.
 */
class PayPalProvider implements ProviderInterface
{
    private string|null $accessToken = null;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly bool $sandbox,
    ) {
    }

    public function createCheckout(PaymentRequest $request): Checkout
    {
        $order = $this->request('POST', '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $request->bookingToken,
                'description' => mb_substr($request->description, 0, 127),
                'amount' => ['currency_code' => strtoupper($request->currency), 'value' => $request->decimalAmount()],
            ]],
            'application_context' => [
                'return_url' => $request->returnUrl,
                'cancel_url' => $request->returnUrl,
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ]);

        foreach ($order['links'] as $link) {
            if (\in_array($link['rel'], ['approve', 'payer-action'], true)) {
                return new Checkout($link['href'], $order['id']);
            }
        }

        throw new \RuntimeException('PayPal did not return an approval link');
    }

    public function fetchStatus(string $reference): string
    {
        $order = $this->request('GET', '/v2/checkout/orders/'.rawurlencode($reference));

        // The payer approved the order on PayPal, take the money now
        if ('APPROVED' === $order['status']) {
            $order = $this->request('POST', '/v2/checkout/orders/'.rawurlencode($reference).'/capture', []);
        }

        return match ($order['status']) {
            'COMPLETED' => PaymentStatus::PAID,
            'VOIDED' => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };
    }

    public function refund(string $reference, int $amount, string $currency): void
    {
        $order = $this->request('GET', '/v2/checkout/orders/'.rawurlencode($reference));
        $captureId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

        if (null === $captureId) {
            throw new \RuntimeException('The PayPal order has no captured payment to refund');
        }

        $this->request('POST', '/v2/payments/captures/'.rawurlencode($captureId).'/refund', [
            'amount' => ['currency_code' => strtoupper($currency), 'value' => PaymentRequest::decimal($amount)],
        ]);
    }

    public function getWebhookReference(Request $request): string|null
    {
        $event = json_decode($request->getContent(), true);
        $resource = $event['resource'] ?? [];

        // Order events carry the order ID, capture events link to it
        return $resource['supplementary_data']['related_ids']['order_id'] ?? (str_starts_with((string) ($event['event_type'] ?? ''), 'CHECKOUT.ORDER') ? ($resource['id'] ?? null) : null);
    }

    private function request(string $method, string $path, array|null $json = null): array
    {
        $options = ['auth_bearer' => $this->getAccessToken()];

        if (null !== $json) {
            $options['json'] = $json ?: new \stdClass();
        }

        return $this->client->request($method, $this->baseUrl().$path, $options)->toArray();
    }

    private function getAccessToken(): string
    {
        return $this->accessToken ??= $this->client->request('POST', $this->baseUrl().'/v1/oauth2/token', [
            'auth_basic' => [$this->clientId, $this->clientSecret],
            'body' => ['grant_type' => 'client_credentials'],
        ])->toArray()['access_token'];
    }

    private function baseUrl(): string
    {
        return $this->sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
    }
}
