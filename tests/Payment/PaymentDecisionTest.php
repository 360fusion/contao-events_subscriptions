<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests\Payment;

use Codefog\EventsSubscriptionsBundle\Payment\PaymentDecision;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use PHPUnit\Framework\TestCase;

class PaymentDecisionTest extends TestCase
{
    public function testAvailableMethods(): void
    {
        $this->assertSame([], PaymentDecision::availableMethods(0, ['card', 'invoice'], true));
        $this->assertSame(['card', 'invoice'], PaymentDecision::availableMethods(1250, ['card', 'invoice'], true));
        $this->assertSame(['invoice'], PaymentDecision::availableMethods(1250, ['card', 'invoice'], false));
        $this->assertSame([], PaymentDecision::availableMethods(1250, ['card'], false));
    }

    public function testDecide(): void
    {
        $both = ['card', 'invoice'];

        $this->assertSame(PaymentStatus::FREE, PaymentDecision::decide([], [], 'a@b.com', 'card', false));
        $this->assertSame(PaymentStatus::FREE, PaymentDecision::decide($both, ['nhs.net'], 'ann@trust.nhs.net', 'card', false));
        $this->assertSame(PaymentStatus::PENDING, PaymentDecision::decide($both, ['nhs.net'], 'ann@gmail.com', 'card', false));
        $this->assertSame(PaymentStatus::INVOICE, PaymentDecision::decide($both, [], 'ann@gmail.com', 'invoice', false));
        $this->assertSame(PaymentStatus::UNPAID, PaymentDecision::decide($both, [], 'ann@gmail.com', 'card', true));
        // An unknown choice falls back to the first method offered
        $this->assertSame(PaymentStatus::INVOICE, PaymentDecision::decide(['invoice'], [], 'ann@gmail.com', 'card', false));
    }
}
