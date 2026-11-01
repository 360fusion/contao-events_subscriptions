<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Creates the provider of a tl_calendar_events_payment_provider record
 */
class ProviderFactory
{
    /**
     * Provider type => credential fields it needs
     */
    public const TYPES = [
        'stripe' => ['secretKey'],
        'paypal' => ['clientId', 'clientSecret'],
        'mollie' => ['apiKey'],
        'govukpay' => ['apiKey'],
    ];

    public static function create(array $record, HttpClientInterface|null $client = null): ProviderInterface
    {
        $client ??= HttpClient::create(['timeout' => 20]);

        return match ($record['type'] ?? null) {
            'stripe' => new StripeProvider($client, (string) $record['secretKey']),
            'paypal' => new PayPalProvider($client, (string) $record['clientId'], (string) $record['clientSecret'], !$record['live']),
            'mollie' => new MollieProvider($client, (string) $record['apiKey']),
            'govukpay' => new GovUkPayProvider($client, (string) $record['apiKey']),
            default => throw new \InvalidArgumentException(sprintf('Unknown payment provider type "%s"', $record['type'] ?? '')),
        };
    }
}
