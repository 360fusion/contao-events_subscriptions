<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\Environment;
use Contao\System;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Component\RateLimiter\Storage\StorageInterface;

/**
 * At most so many bookings from one IP address in ten minutes (Booking settings › General), against bots and mass bookings.
 * Everyone behind one office or hospital network shares an address: leave it off or set it high there.
 */
final class BookingRateLimit
{
    /**
     * @param int    $max Bookings per ten minutes, 0 for no limit
     * @param string $key Who is booking, normally the IP address
     */
    public static function allow(int $max, string $key, StorageInterface|null $storage = null): bool
    {
        if ($max < 1) {
            return true;
        }

        $factory = new RateLimiterFactory(
            ['id' => 'events_subscriptions_booking', 'policy' => 'sliding_window', 'limit' => $max, 'interval' => '10 minutes'],
            $storage ?? new CacheStorage(System::getContainer()->get('cache.app')),
        );

        return $factory->create($key)->consume()->isAccepted();
    }

    public static function allowRequest(): bool
    {
        return self::allow(Features::bookingLimit(), (string) Environment::get('ip'));
    }
}
