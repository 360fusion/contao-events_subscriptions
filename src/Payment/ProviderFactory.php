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

    /**
     * The credential fields of the provider that are still empty ("type" when no provider was chosen)
     *
     * @return list<string>
     */
    public static function missingCredentials(array $record): array
    {
        if (!isset(self::TYPES[$record['type'] ?? ''])) {
            return ['type'];
        }

        return array_values(array_filter(self::TYPES[$record['type']], static fn (string $field) => '' === trim((string) ($record[$field] ?? ''))));
    }

    /**
     * A provider can only be used once its account details are entered
     */
    public static function hasCredentials(array $record): bool
    {
        return [] === self::missingCredentials($record);
    }

    /**
     * Test or live: PayPal has a switch, the others follow the key (empty when it cannot be told)
     */
    public static function mode(array $record): string
    {
        if ('paypal' === ($record['type'] ?? '')) {
            return $record['live'] ? 'live' : 'test';
        }

        $key = (string) ('stripe' === ($record['type'] ?? '') ? ($record['secretKey'] ?? '') : ($record['apiKey'] ?? ''));

        return match (true) {
            str_contains($key, '_live_'), str_starts_with($key, 'live_') => 'live',
            str_contains($key, '_test_'), str_starts_with($key, 'test_') => 'test',
            default => '',
        };
    }

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
