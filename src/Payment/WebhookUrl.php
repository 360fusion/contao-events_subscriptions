<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Contao\System;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class WebhookUrl
{
    public const ROUTE = 'events_subscriptions_payment_webhook';

    /**
     * The URL to enter at the provider for the payment status notifications
     */
    public static function generate(int $providerId): string
    {
        return System::getContainer()->get('router')->generate(self::ROUTE, ['id' => $providerId], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
