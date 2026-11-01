<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\CalendarEventsModel;
use Contao\Database;
use Contao\StringUtil;

/**
 * Venues and locations (Events › Venues and locations). Choosing a venue on an event fills in the event's location and address,
 * which every list, the event page, the calendar file and the e-mails already show.
 */
final class Venue
{
    public static function find(int $id): array|null
    {
        $row = Database::getInstance()->prepare('SELECT * FROM tl_calendar_events_venue WHERE id=?')->execute($id);

        return $row->numRows ? $row->row() : null;
    }

    /**
     * "Street, Town, Postcode" for the single address line of an event
     */
    public static function address(array $venue): string
    {
        return mb_substr(implode(', ', array_filter(array_map('trim', [$venue['street'], $venue['town'], $venue['postcode']]), static fn (string $part) => '' !== $part)), 0, 255);
    }

    /**
     * Copy the name and address of a venue to its events (all of them, or one), so the website shows the venue as it is now
     */
    public static function syncEvents(int $venueId, int|null $eventId = null): void
    {
        if (null === ($venue = self::find($venueId))) {
            return;
        }

        $events = $eventId ? CalendarEventsModel::findBy(['id=?', 'venue=?'], [$eventId, $venueId]) : CalendarEventsModel::findBy('venue', $venueId);

        foreach ($events ?? [] as $event) {
            $location = mb_substr((string) $venue['title'], 0, 255);
            $address = self::address($venue);

            if ($event->location !== $location || $event->address !== $address) {
                $event->location = $location;
                $event->address = $address;
                $event->save();
            }
        }
    }

    /**
     * Directions, accessibility and map link for the event page: shown under the location
     */
    public static function info(array $venue): string
    {
        $L = $GLOBALS['TL_LANG']['MSC'];
        $parts = [];

        foreach (['directions' => 'venueDirections', 'accessibility' => 'venueAccessibility'] as $field => $label) {
            if ('' !== trim((string) $venue[$field])) {
                $parts[] = '<p class="venue-'.$field.'"><strong>'.$L['events_subscriptions.'.$label].'</strong> '.nl2br(StringUtil::specialchars($venue[$field]), false).'</p>';
            }
        }

        // Only a web address: nothing else may end up in a link
        if (preg_match('#^https?://#i', (string) $venue['mapLink'])) {
            $parts[] = '<p class="venue-map"><a href="'.StringUtil::specialchars($venue['mapLink']).'" target="_blank" rel="noopener">'.$L['events_subscriptions.venueMap'].'</a></p>';
        }

        return $parts ? '<div class="venue-info">'.implode('', $parts).'</div>' : '';
    }
}
