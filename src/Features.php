<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\Config;

/**
 * Feature switches of Events › Settings › Booking settings
 */
final class Features
{
    public static function payments(): bool
    {
        return (bool) Config::get('eventsSubscriptionsPayments');
    }

    /**
     * On unless switched off
     */
    public static function attendance(): bool
    {
        $value = Config::get('eventsSubscriptionsAttendance');

        return null === $value || (bool) $value;
    }

    /**
     * Certificates of attendance: on unless switched off, and only possible when attendance is recorded
     */
    public static function certificates(): bool
    {
        $value = Config::get('eventsSubscriptionsCertificate');

        return self::attendance() && (null === $value || (bool) $value);
    }

    public static function oneBookingPerCourse(): bool
    {
        return (bool) Config::get('eventsSubscriptionsOneBookingPerCourse');
    }

    /**
     * Bookings missed before someone is listed as a repeat no-show
     */
    public static function noShowLimit(): int
    {
        return max(1, (int) Config::get('eventsSubscriptionsNoShowLimit') ?: 3);
    }

    /**
     * The hour of the day when the trainer packs go out for tomorrow's events
     */
    public static function trainerHour(): int
    {
        $hour = Config::get('eventsSubscriptionsTrainerHour');

        return '' === (string) $hour ? 16 : min(23, max(0, (int) $hour));
    }

    /**
     * A multi-line setting: the system configuration cannot store line breaks, they are kept as "\n"
     */
    public static function text(string $key): string
    {
        return str_replace('\\n', "\n", (string) Config::get($key));
    }

    public static function calendarGroups(): bool
    {
        return (bool) Config::get('eventsSubscriptionsCalendarGroups');
    }

    /**
     * The booking form texts that can be changed, as language keys => setting
     */
    public const TEXTS = [
        'events_subscriptions.formIntro' => 'eventsSubscriptionsTextIntro',
        'events_subscriptions.subscriptionClosed' => 'eventsSubscriptionsTextClosed',
        'events_subscriptions.eventFull' => 'eventsSubscriptionsTextFull',
        'events_subscriptions.eventFullWaitingList' => 'eventsSubscriptionsTextWaitingList',
        'events_subscriptions.subscribeConfirmation' => 'eventsSubscriptionsTextConfirmation',
    ];
}
