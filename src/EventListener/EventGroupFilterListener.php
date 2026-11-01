<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\EventGroupFilter;
use Contao\Database;
use Contao\Input;
use Contao\Module;

class EventGroupFilterListener
{
    /**
     * Filter the events of modules with "Filter by URL" by ?calendar=, ?group= and ?month=
     */
    public function onGetAllEvents(array $allEvents, array $calendars, $start, $end, Module $module): array
    {
        if (!$module->cal_filterByUrl) {
            return $allEvents;
        }

        $month = (string) Input::get('month');

        return EventGroupFilter::filter(
            $allEvents,
            EventGroupFilter::resolve(Input::get('calendar'), EventGroupFilter::idsByAlias(Database::getInstance()->query('SELECT id, alias FROM tl_calendar')->fetchAllAssoc())),
            EventGroupFilter::resolve(Input::get('group'), EventGroupFilter::idsByAlias(Database::getInstance()->query('SELECT id, alias FROM tl_calendar_event_group WHERE published=1')->fetchAllAssoc())),
            preg_match('/^\d{6}$/', $month) ? $month : null,
        );
    }
}
