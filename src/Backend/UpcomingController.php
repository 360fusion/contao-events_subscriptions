<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Database;
use Contao\Input;
use Contao\Pagination;
use Contao\System;

/**
 * Upcoming events of all calendars with subscriptions enabled, and their subscriptions.
 */
class UpcomingController extends OverviewController
{
    /**
     * Published events that have not ended yet, in calendars with subscriptions enabled
     */
    public const LIVE_EVENTS_WHERE = "tl_calendar_events.published=1 AND (tl_calendar_events.start='' OR tl_calendar_events.start<=UNIX_TIMESTAMP()) AND (tl_calendar_events.stop='' OR tl_calendar_events.stop>UNIX_TIMESTAMP()) AND GREATEST(tl_calendar_events.startTime, tl_calendar_events.endTime)>=UNIX_TIMESTAMP() AND tl_calendar_events.pid IN (SELECT id FROM tl_calendar WHERE subscription_enable=1)";

    public function run()
    {
        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_calendar_events');

        $db = Database::getInstance();
        $total = (int) $db->query('SELECT COUNT(*) AS total FROM tl_calendar_events WHERE '.self::LIVE_EVENTS_WHERE)->total;

        $limit = Config::get('resultsPerPage');
        $page = (Input::get('esp') ?: 1) - 1;
        $pagination = new Pagination($total, $limit, 7, 'esp', new BackendTemplate('be_pagination'), true);

        $events = $db
            ->prepare('SELECT * FROM tl_calendar_events WHERE '.self::LIVE_EVENTS_WHERE.' ORDER BY startTime')
            ->limit($limit, $page * $limit)
            ->execute();

        $template = new BackendTemplate('be_events_subscriptions_overview');
        $template->backUrl = Backend::getReferer();
        $template->headline = $GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.headline'];
        $template->showCalendar = true;
        $template->entries = $this->createEntries($events, false);
        $template->pagination = $pagination->generate();

        return $template->parse();
    }
}
