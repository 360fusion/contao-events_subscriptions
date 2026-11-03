<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\BookingRateLimit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class BookingRateLimitTest extends TestCase
{
    public function testNoLimitWhenSwitchedOff(): void
    {
        $storage = new InMemoryStorage();

        for ($i = 0; $i < 50; ++$i) {
            $this->assertTrue(BookingRateLimit::allow(0, '10.0.0.1', $storage));
        }
    }

    public function testStopsAfterTheLimitForOneAddressOnly(): void
    {
        $storage = new InMemoryStorage();

        $this->assertTrue(BookingRateLimit::allow(2, '10.0.0.1', $storage));
        $this->assertTrue(BookingRateLimit::allow(2, '10.0.0.1', $storage));
        $this->assertFalse(BookingRateLimit::allow(2, '10.0.0.1', $storage));
        $this->assertTrue(BookingRateLimit::allow(2, '10.0.0.2', $storage), 'another address is not affected');
    }
}
