<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Mollie Payments API (https://docs.mollie.com/reference/create-payment). Test or live mode follows the API key.
 */
class MollieProvider implements ProviderInterface
{
    private const API = 'https://api.mollie.com/v2';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
    ) {
    }

    public function createCheckout(PaymentRequest $request): Checkout
    {
        $body = [
            'amount' => ['currency' => strtoupper($request->currency), 'value' => $request->decimalAmount()],
            'description' => mb_substr($request->description, 0, 255),
            'redirectUrl' => $request->returnUrl,
            'cancelUrl' => $request->returnUrl,
            'metadata' => ['booking' => $request->bookingToken],
        ];

        // Mollie cannot reach a local development site, the return URL confirms the payment there
        if (!self::isLocal($request->webhookUrl)) {
            $body['webhookUrl'] = $request->webhookUrl;
        }

        $payment = $this->request('POST', '/payments', $body);

        return new Checkout($payment['_links']['checkout']['href'], $payment['id']);
    }

    public function fetchStatus(string $reference): string
    {
        return match ($this->request('GET', '/payments/'.rawurlencode($reference))['status']) {
            'paid' => PaymentStatus::PAID,
            'failed', 'canceled', 'expired' => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };
    }

    public function refund(string $reference, int $amount, string $currency): void
    {
        $this->request('POST', '/payments/'.rawurlencode($reference).'/refunds', [
            'amount' => ['currency' => strtoupper($currency), 'value' => PaymentRequest::decimal($amount)],
        ]);
    }

    public function getWebhookReference(Request $request): string|null
    {
        return $request->request->get('id') ?: null;
    }

    public static function isLocal(string $url): bool
    {
        $host = (string) parse_url($url, PHP_URL_HOST);

        return \in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($host, '.test') || str_ends_with($host, '.localhost');
    }

    private function request(string $method, string $path, array $json = []): array
    {
        return $this->client->request($method, self::API.$path, ['auth_bearer' => $this->apiKey] + ($json ? ['json' => $json] : []))->toArray();
    }
}
