<?php

namespace Codefog\EventsSubscriptionsBundle\EventListener;

/**
 * Runs after Contao's UserRootListener (priority -64), which adds the "Back end permissions" field on load
 */
class NewCalendarPermissionsListener
{
    public function __invoke(string $table): void
    {
        if ('tl_calendar' === $table) {
            $GLOBALS['TL_DCA']['tl_calendar']['config']['onload_callback'][] = ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'preselectPermissions'];
        }
    }
}
