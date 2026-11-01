<?php

namespace Codefog\EventsSubscriptionsBundle\Email;

/**
 * The tags (notification tokens) people can insert into the event e-mails, grouped and with readable names
 */
final class EmailTags
{
    /**
     * Group => token => label key in $GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags']
     */
    private const CATALOGUE = [
        'booking' => ['subscription_firstname', 'subscription_lastname', 'subscription_email', 'subscription_numberOfParticipants'],
        'event' => ['event_title', 'event_startDate', 'event_startTime', 'event_endDate', 'event_endTime', 'event_location', 'event_address', 'event_teaser', 'days_before_event', 'calendar_title'],
        'links' => ['event_link', 'event_ics_link', 'unsubscribe_link'],
        'payment' => ['payment_amount', 'payment_status', 'payment_method', 'purchase_order', 'payment_link'],
    ];

    /**
     * @param list<string>          $available     Token names of the notification type, wildcards like "event_*" included
     * @param array<string, string> $registration  Registration field tokens => labels (subscription_field_*)
     * @param array<string, string> $labels        Token => label
     *
     * @return array<string, array<string, string>> Group => token => label, only the tokens the type provides
     */
    public static function forType(array $available, array $registration, array $labels): array
    {
        $groups = [];

        foreach (self::CATALOGUE as $group => $tokens) {
            foreach ($tokens as $token) {
                if (self::isAvailable($token, $available)) {
                    $groups[$group][$token] = $labels[$token] ?? $token;
                }
            }

            // The registration fields belong to the booking
            if ('booking' === $group) {
                foreach ($registration as $token => $label) {
                    if (self::isAvailable($token, $available)) {
                        $groups[$group][$token] = $label;
                    }
                }
            }
        }

        return $groups;
    }

    private static function isAvailable(string $token, array $available): bool
    {
        foreach ($available as $name) {
            if ($name === $token || (str_ends_with($name, '*') && str_starts_with($token, substr($name, 0, -1)))) {
                return true;
            }
        }

        return false;
    }
}
