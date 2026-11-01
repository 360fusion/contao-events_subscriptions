<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests\DataContainer;

use Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionFieldContainer;
use PHPUnit\Framework\TestCase;

class SubscriptionFieldContainerTest extends TestCase
{
    public function testGenerateKey(): void
    {
        $c = new SubscriptionFieldContainer();
        $this->assertSame('ward_area', $c->generateKey('Ward / Area', []));
        $this->assertSame('job_role_2', $c->generateKey('Job Role', ['job_role']));
        $this->assertSame('dietary_requirements', $c->generateKey('  Dietary requirements! ', []));
        $this->assertSame('field', $c->generateKey('***', []));
    }

    public function testGenerateKeySkipsEveryTakenSuffix(): void
    {
        $c = new SubscriptionFieldContainer();
        $this->assertSame('role_3', $c->generateKey('Role', ['role', 'role_2']));
    }

    public function testGenerateKeyIsAtMost64Characters(): void
    {
        $c = new SubscriptionFieldContainer();
        $this->assertSame(64, \strlen($c->generateKey(str_repeat('a', 100), [])));
        $this->assertLessThanOrEqual(64, \strlen($c->generateKey(str_repeat('a', 100), [str_repeat('a', 64)])));
    }
}
