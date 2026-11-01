<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Database;
use Contao\Environment;
use Contao\Input;
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

        $filters = $this->readFilters() + [
            'calendar' => (int) Input::get('calendar'),
            'group' => Config::get('eventsSubscriptionsCalendarGroups') ? (int) Input::get('group') : 0,
        ];

        $where = [self::LIVE_EVENTS_WHERE];
        $values = [];
        $this->addFilterConditions($filters, $where, $values);

        if ($filters['calendar']) {
            $where[] = 'tl_calendar_events.pid=?';
            $values[] = $filters['calendar'];
        }

        if ($filters['group']) {
            $where[] = 'tl_calendar_events.pid IN (SELECT id FROM tl_calendar WHERE calendarGroup=?)';
            $values[] = $filters['group'];
        }

        $events = Database::getInstance()
            ->prepare('SELECT * FROM tl_calendar_events WHERE '.implode(' AND ', $where).' ORDER BY startTime')
            ->execute(...$values);

        $limit = (int) Config::get('resultsPerPage');
        [$entries, $total, $subscriptions] = $this->filterByPlaces($events, $filters['places'], $limit, max(1, (int) Input::get('esp')), false);

        $template = new BackendTemplate('be_events_subscriptions_overview');
        $template->backUrl = Backend::getReferer();
        $template->headline = $GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.headline'];
        $template->showCalendar = true;
        $template->entries = $entries;
        $template->pagination = BackendPagination::render($total, $limit, 'esp');
        $template->filters = $filters;
        $template->filterAction = strtok(Environment::get('requestUri'), '?');
        $template->filterHidden = ['do' => Input::get('do'), 'key' => Input::get('key'), 'rt' => Input::get('rt')];
        $template->calendarOptions = $this->getOptions('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title');
        $template->eventGroupOptions = $this->getEventGroupOptions();
        $template->groupOptions = Config::get('eventsSubscriptionsCalendarGroups') ? $this->getOptions('SELECT id, title FROM tl_calendar_group ORDER BY title') : [];
        $template->summary = sprintf($GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.summary'], number_format($total), number_format($subscriptions));

        return $template->parse();
    }

    /**
     * @return array<int, string>
     */
    private function getOptions(string $query): array
    {
        $options = [];
        $result = Database::getInstance()->query($query);

        while ($result->next()) {
            $options[$result->id] = '' !== (string) $result->title ? $result->title : '#'.$result->id;
        }

        return $options;
    }
}
