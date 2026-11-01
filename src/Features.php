<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\Config;

/**
 * Feature switches of Events › Setup › Booking settings
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
