<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle;

/**
 * Filters the result of Contao's getAllEvents ([Ymd][begin][] => event) by calendar, event group and month.
 */
class EventGroupFilter
{
    /**
     * ID for an alias that does not exist, so the list shows nothing instead of everything
     */
    public const UNKNOWN = -1;

    /**
     * @param int|null    $calendar Calendar ID, null for any
     * @param int|null    $group    Event group ID, null for any
     * @param string|null $month    Month as YYYYMM, null for any
     */
    /**
     * Resolve "?calendar=" and "?group=" values (alias or ID) to IDs: null when empty, UNKNOWN when not found.
     *
     * @param array<string, int> $idsByAlias Alias => ID of the existing records
     */
    public static function resolve(string|null $value, array $idsByAlias): int|null
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (isset($idsByAlias[$value])) {
            return $idsByAlias[$value];
        }

        return preg_match('/^[1-9]\d*$/', $value) && \in_array((int) $value, $idsByAlias, true) ? (int) $value : self::UNKNOWN;
    }

    /**
     * @param list<array{id: int|string, alias: string}> $rows
     *
     * @return array<string, int> Alias (or the ID if there is no alias) => ID
     */
    public static function idsByAlias(array $rows): array
    {
        $ids = [];

        foreach ($rows as $row) {
            $ids['' !== (string) $row['alias'] ? $row['alias'] : (string) $row['id']] = (int) $row['id'];
        }

        return $ids;
    }

    public static function filter(array $allEvents, int|null $calendar, int|null $group, string|null $month): array
    {
        foreach ($allEvents as $day => $times) {
            if (null !== $month && !str_starts_with((string) $day, $month)) {
                unset($allEvents[$day]);
                continue;
            }

            foreach ($times as $time => $events) {
                $events = array_filter($events, static fn (array $event) => (null === $calendar || (int) $event['pid'] === $calendar) && (null === $group || (int) ($event['eventGroup'] ?? 0) === $group));

                if ($events) {
                    $allEvents[$day][$time] = array_values($events);
                } else {
                    unset($allEvents[$day][$time]);
                }
            }

            if (empty($allEvents[$day])) {
                unset($allEvents[$day]);
            }
        }

        return $allEvents;
    }
}
