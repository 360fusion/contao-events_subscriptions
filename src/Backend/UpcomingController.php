<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
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

        $filters = [
            'search' => trim((string) Input::get('search')),
            'calendar' => (int) Input::get('calendar'),
            'group' => Config::get('eventsSubscriptionsCalendarGroups') ? (int) Input::get('group') : 0,
            'from' => trim((string) Input::get('from')),
            'to' => trim((string) Input::get('to')),
            'places' => \in_array(Input::get('places'), ['full', 'available'], true) ? Input::get('places') : '',
        ];

        $where = [self::LIVE_EVENTS_WHERE];
        $values = [];

        if ('' !== $filters['search']) {
            $where[] = 'tl_calendar_events.title LIKE ?';
            $values[] = '%'.$filters['search'].'%';
        }

        if ($filters['calendar']) {
            $where[] = 'tl_calendar_events.pid=?';
            $values[] = $filters['calendar'];
        }

        if ($filters['group']) {
            $where[] = 'tl_calendar_events.pid IN (SELECT id FROM tl_calendar WHERE calendarGroup=?)';
            $values[] = $filters['group'];
        }

        if (null !== ($from = $this->parseDate($filters['from']))) {
            $where[] = 'tl_calendar_events.startTime>=?';
            $values[] = $from;
        }

        if (null !== ($to = $this->parseDate($filters['to']))) {
            $where[] = 'tl_calendar_events.startTime<?';
            $values[] = strtotime('+1 day', $to);
        }

        $events = Database::getInstance()
            ->prepare('SELECT * FROM tl_calendar_events WHERE '.implode(' AND ', $where).' ORDER BY startTime')
            ->execute(...$values);

        // The places filter needs the subscription counts, so it is applied to the built rows
        $rows = [];

        foreach ($this->createEntries($events, false) as $group) {
            foreach ($group['events'] as $event) {
                $full = $event['maxSubscriptions'] > 0 && $event['subscriptions'] >= $event['maxSubscriptions'];

                if (('full' === $filters['places'] && !$full) || ('available' === $filters['places'] && $full)) {
                    continue;
                }

                $rows[] = ['group' => $group['label'], 'event' => $event];
            }
        }

        $limit = (int) Config::get('resultsPerPage');
        $page = max(1, (int) Input::get('esp'));
        $pagination = new Pagination(\count($rows), $limit, 7, 'esp', new BackendTemplate('be_pagination'), true);
        $entries = [];

        foreach (\array_slice($rows, ($page - 1) * $limit, $limit) as $row) {
            $entries[$row['group']]['label'] = $row['group'];
            $entries[$row['group']]['events'][] = $row['event'];
        }

        $template = new BackendTemplate('be_events_subscriptions_overview');
        $template->backUrl = Backend::getReferer();
        $template->headline = $GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.headline'];
        $template->showCalendar = true;
        $template->entries = $entries;
        $template->pagination = $pagination->generate();
        $template->filters = $filters;
        $template->filterAction = strtok(Environment::get('requestUri'), '?');
        $template->filterHidden = ['do' => Input::get('do'), 'key' => Input::get('key'), 'rt' => Input::get('rt')];
        $template->calendarOptions = $this->getOptions('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title');
        $template->groupOptions = Config::get('eventsSubscriptionsCalendarGroups') ? $this->getOptions('SELECT id, title FROM tl_calendar_group ORDER BY title') : [];
        $template->summary = sprintf(
            $GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.summary'],
            number_format(\count($rows)),
            number_format(array_sum(array_map(static fn ($row) => $row['event']['subscriptions'], $rows))),
        );

        return $template->parse();
    }

    private function parseDate(string $value): int|null
    {
        if ('' === $value) {
            return null;
        }

        try {
            return (new Date($value, Date::getNumericDateFormat()))->tstamp;
        } catch (\OutOfBoundsException) {
            return null;
        }
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
