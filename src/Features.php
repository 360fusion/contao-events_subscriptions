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

    /**
     * Venues and locations: a list of places, chosen on the event
     */
    public static function venues(): bool
    {
        return (bool) Config::get('eventsSubscriptionsVenues');
    }

    /**
     * The Payments list (payments, refunds, invoices) in the left menu: shown unless switched off; hidden, it opens from a tile on the Settings page
     */
    public static function paymentsMenu(): bool
    {
        $value = Config::get('eventsSubscriptionsPaymentsMenu');

        return null === $value || (bool) $value;
    }

    /**
     * Paying by invoice or purchase order: on unless switched off
     */
    public static function invoice(): bool
    {
        $value = Config::get('eventsSubscriptionsInvoice');

        return null === $value || (bool) $value;
    }

    /**
     * How long an unpaid card booking keeps its place: 30 minutes (the shortest a provider's payment page lasts) up to a day
     */
    public static function paymentHoldMinutes(): int
    {
        $minutes = (int) Config::get('eventsSubscriptionsPaymentHold');

        return $minutes < 30 ? 30 : min($minutes, 1440);
    }

    /**
     * Trainers: a list of people, chosen on events and courses
     */
    public static function trainers(): bool
    {
        return (bool) Config::get('eventsSubscriptionsTrainers');
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
