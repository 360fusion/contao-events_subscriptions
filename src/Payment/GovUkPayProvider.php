<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * GOV.UK Pay (https://docs.payments.service.gov.uk/api_reference/). GBP only, test or live follows the API key.
 */
class GovUkPayProvider implements ProviderInterface
{
    private const API = 'https://publicapi.payments.service.gov.uk/v1';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiKey,
    ) {
    }

    public function createCheckout(PaymentRequest $request): Checkout
    {
        $payment = $this->request('POST', '/payments', [
            'amount' => $request->amount,
            'reference' => $request->bookingToken,
            'description' => mb_substr($request->description, 0, 255),
            'return_url' => $request->returnUrl,
        ] + ('' !== $request->email ? ['email' => $request->email] : []));

        return new Checkout($payment['_links']['next_url']['href'], $payment['payment_id']);
    }

    public function fetchStatus(string $reference): string
    {
        return match ($this->request('GET', '/payments/'.rawurlencode($reference))['state']['status']) {
            'success' => PaymentStatus::PAID,
            'failed', 'cancelled', 'error' => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };
    }

    public function refund(string $reference, int $amount, string $currency): void
    {
        $payment = $this->request('GET', '/payments/'.rawurlencode($reference));

        $this->request('POST', '/payments/'.rawurlencode($reference).'/refunds', [
            'amount' => $amount,
            'refund_amount_available' => $payment['refund_summary']['amount_available'],
        ]);
    }

    public function getWebhookReference(Request $request): string|null
    {
        $event = json_decode($request->getContent(), true);

        return $event['resource_id'] ?? $event['resource']['payment_id'] ?? null;
    }

    private function request(string $method, string $path, array $json = []): array
    {
        return $this->client->request($method, self::API.$path, ['auth_bearer' => $this->apiKey] + ($json ? ['json' => $json] : []))->toArray();
    }
}
