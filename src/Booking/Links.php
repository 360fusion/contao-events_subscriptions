<?php

namespace Codefog\EventsSubscriptionsBundle\Booking;

use Contao\Environment;
use Contao\System;

/**
 * The addresses of the booker's own pages. The booking's secret token is in the link, like the unsubscribe link.
 */
final class Links
{
    public const BOOKING = 'events_subscriptions_booking';
    public const CERTIFICATE = 'events_subscriptions_certificate';
    public const FEEDBACK = 'events_subscriptions_feedback';
    public const CHECKIN = 'events_subscriptions_checkin';
    public const CONFIRM = 'events_subscriptions_confirm';

    /**
     * The e-mail tokens ##booking_link##, ##certificate_link## and ##feedback_link##
     *
     * @param string $eventLink The absolute event page address: the cron has no request to take the host from
     */
    public static function tokens(string $bookingToken, string $eventLink): array
    {
        if ('' === $bookingToken) {
            return [];
        }

        $base = preg_match('#^https?://[^/]+#', $eventLink, $match) ? $match[0] : rtrim(Environment::get('url'), '/');

        return [
            'booking_link' => $base.self::path(self::BOOKING, $bookingToken),
            'certificate_link' => $base.self::path(self::CERTIFICATE, $bookingToken),
            'feedback_link' => $base.self::path(self::FEEDBACK, $bookingToken),
            'confirm_link' => $base.self::path(self::CONFIRM, $bookingToken),
        ];
    }

    public static function path(string $route, string $token): string
    {
        return System::getContainer()->get('router')->generate($route, ['token' => $token]);
    }

    public static function absolute(string $route, string $token): string
    {
        return rtrim(Environment::get('url'), '/').self::path($route, $token);
    }
}
