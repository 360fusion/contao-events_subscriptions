<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests\Payment;

use Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory;
use PHPUnit\Framework\TestCase;

class ProviderCredentialsTest extends TestCase
{
    public function testAProviderNeedsTheDetailsOfItsType(): void
    {
        $this->assertSame(['secretKey'], ProviderFactory::missingCredentials(['type' => 'stripe', 'secretKey' => '']));
        $this->assertSame(['secretKey'], ProviderFactory::missingCredentials(['type' => 'stripe', 'secretKey' => '   ']));
        $this->assertTrue(ProviderFactory::hasCredentials(['type' => 'stripe', 'secretKey' => 'sk_test_1']));

        // PayPal needs both
        $this->assertSame(['clientSecret'], ProviderFactory::missingCredentials(['type' => 'paypal', 'clientId' => 'abc', 'clientSecret' => '']));
        $this->assertSame(['clientId', 'clientSecret'], ProviderFactory::missingCredentials(['type' => 'paypal']));
        $this->assertTrue(ProviderFactory::hasCredentials(['type' => 'paypal', 'clientId' => 'a', 'clientSecret' => 'b']));

        $this->assertSame(['apiKey'], ProviderFactory::missingCredentials(['type' => 'mollie', 'apiKey' => '']));
        $this->assertTrue(ProviderFactory::hasCredentials(['type' => 'govukpay', 'apiKey' => 'api_test_1']));
    }

    public function testNoProviderTypeMeansNothingToUse(): void
    {
        $this->assertSame(['type'], ProviderFactory::missingCredentials(['type' => '']));
        $this->assertSame(['type'], ProviderFactory::missingCredentials([]));
        $this->assertFalse(ProviderFactory::hasCredentials(['type' => 'unknown', 'apiKey' => 'x']));
    }

    public function testTestOrLiveFromTheKeyOrTheSwitch(): void
    {
        $this->assertSame('test', ProviderFactory::mode(['type' => 'stripe', 'secretKey' => 'sk_test_abc']));
        $this->assertSame('live', ProviderFactory::mode(['type' => 'stripe', 'secretKey' => 'sk_live_abc']));
        $this->assertSame('test', ProviderFactory::mode(['type' => 'mollie', 'apiKey' => 'test_abc']));
        $this->assertSame('live', ProviderFactory::mode(['type' => 'mollie', 'apiKey' => 'live_abc']));
        $this->assertSame('live', ProviderFactory::mode(['type' => 'govukpay', 'apiKey' => 'api_live_abc']));
        $this->assertSame('', ProviderFactory::mode(['type' => 'govukpay', 'apiKey' => 'something']));
        $this->assertSame('live', ProviderFactory::mode(['type' => 'paypal', 'live' => '1']));
        $this->assertSame('test', ProviderFactory::mode(['type' => 'paypal', 'live' => '']));
    }
}
