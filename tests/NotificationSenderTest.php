<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\NotificationSender;
use Codefog\EventsSubscriptionsBundle\SubscriptionFactory;
use Codefog\HasteBundle\Formatter;
use Contao\System;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class NotificationSenderTest extends TestCase
{
    public function testLoginSecretsNeverBecomeTokens(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->method('dcaValue')->willReturnCallback(static fn (string $table, string $field, mixed $value) => $value);

        $container = new Container();
        $container->set(Formatter::class, $formatter);
        System::setContainer($container);

        $sender = new NotificationSender($this->createMock(SubscriptionFactory::class));
        $tokens = $sender->getTokens(['firstname' => 'Ann', 'email' => 'ann@example.test', 'password' => '$2y$hash', 'secret' => 'totp', 'backupCodes' => 'codes', 'session' => 'x'], 'tl_member', 'member_');

        $this->assertSame(['member_firstname' => 'Ann', 'member_email' => 'ann@example.test'], $tokens);
    }
}
