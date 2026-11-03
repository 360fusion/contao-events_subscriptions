<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;

/**
 * Which calendars the signed-in back end user may work with: the booking tools check this too, so changing the ID in the
 * address bar does not open the bookings of a calendar the user cannot see.
 */
final class Access
{
    public static function calendar(int $calendarId): bool
    {
        $user = BackendUser::getInstance();

        return $user->isAdmin || $user->hasAccess($calendarId, 'calendars');
    }

    /**
     * @throws AccessDeniedException
     */
    public static function requireCalendar(int $calendarId): void
    {
        if (!self::calendar($calendarId)) {
            throw new AccessDeniedException(sprintf('Not enough permissions to work with the bookings of calendar ID %d.', $calendarId));
        }
    }

    /**
     * SQL to add after WHERE ... AND so that only the calendars of the user show up, e.g. "e.pid IN (3,5)"; empty for admins
     */
    public static function calendarSql(string $column): string
    {
        $user = BackendUser::getInstance();

        if ($user->isAdmin) {
            return '';
        }

        $ids = array_values(array_filter(array_map('intval', (array) $user->calendars)));

        return $ids ? $column.' IN ('.implode(',', $ids).')' : '1=0';
    }
}
