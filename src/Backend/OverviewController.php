<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Database;
use Contao\Database\Result;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\Model\Collection;
use Contao\Pagination;
use Contao\System;

class OverviewController
{
    /**
     * Run the controller
     *
     * @return string
     */
    public function run()
    {
        if (Input::get('key') !== 'subscriptions_overview'
            || ($calendarModel = CalendarModel::findByPk(Input::get('id'))) === null
            || !$calendarModel->subscription_enable
        ) {
            Controller::redirect('contao/main.php?act=error');
        }

        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_calendar_events');

        return $this->createTemplate($calendarModel)->parse();
    }

    /**
     * Create the template: the calendar's events with subscriptions, newest first, with filters and search
     *
     * @param CalendarModel $calendarModel
     *
     * @return BackendTemplate
     */
    protected function createTemplate(CalendarModel $calendarModel)
    {
        $filters = $this->readFilters();
        $filters['when'] = \in_array(Input::get('when'), ['upcoming', 'past'], true) ? Input::get('when') : '';

        $where = ['tl_calendar_events.pid=?', 'EXISTS(SELECT 1 FROM tl_calendar_events_subscription WHERE tl_calendar_events_subscription.pid=tl_calendar_events.id)'];
        $values = [$calendarModel->id];
        $this->addFilterConditions($filters, $where, $values);

        if ('upcoming' === $filters['when']) {
            $where[] = 'GREATEST(tl_calendar_events.startTime, tl_calendar_events.endTime)>=UNIX_TIMESTAMP()';
        } elseif ('past' === $filters['when']) {
            $where[] = 'GREATEST(tl_calendar_events.startTime, tl_calendar_events.endTime)<UNIX_TIMESTAMP()';
        }

        $db = Database::getInstance();
        $limit = (int) Config::get('resultsPerPage');
        $page = max(1, (int) Input::get('esp'));
        $sql = 'FROM tl_calendar_events WHERE '.implode(' AND ', $where);

        if ('' === $filters['places']) {
            // Page in the database, a calendar can have hundreds of events with thousands of subscriptions
            $total = (int) $db->prepare("SELECT COUNT(*) AS total $sql")->execute(...$values)->total;
            $subscriptions = (int) $db->prepare("SELECT SUM(numberOfParticipants) AS total FROM tl_calendar_events_subscription WHERE pid IN (SELECT id $sql)")->execute(...$values)->total;
            $entries = $this->createEntries($db->prepare("SELECT * $sql ORDER BY startTime DESC")->limit($limit, ($page - 1) * $limit)->execute(...$values));
        } else {
            [$entries, $total, $subscriptions] = $this->filterByPlaces($db->prepare("SELECT * $sql ORDER BY startTime DESC")->execute(...$values), $filters['places'], $limit, $page, true);
        }

        $template = new BackendTemplate('be_events_subscriptions_overview');
        $template->backUrl = Backend::getReferer();
        $template->calendar = $calendarModel->title;
        $template->entries = $entries;
        $template->pagination = (new Pagination($total, $limit, 7, 'esp', new BackendTemplate('be_pagination'), true))->generate();
        $template->filters = $filters;
        $template->filterAction = strtok(Environment::get('requestUri'), '?');
        $template->filterHidden = ['do' => Input::get('do'), 'key' => Input::get('key'), 'id' => Input::get('id'), 'rt' => Input::get('rt')];
        $template->calendarOptions = [];
        $template->groupOptions = [];
        $template->eventGroupOptions = $this->getEventGroupOptions((int) $calendarModel->id);
        $template->summary = sprintf($GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.summary'], number_format($total), number_format($subscriptions));

        return $template;
    }

    /**
     * The filters shared by the calendar overview and the upcoming events page
     */
    protected function readFilters(): array
    {
        return [
            'search' => trim((string) Input::get('search')),
            'eventGroup' => (int) Input::get('eventGroup'),
            'from' => trim((string) Input::get('from')),
            'to' => trim((string) Input::get('to')),
            'places' => \in_array(Input::get('places'), ['full', 'available'], true) ? Input::get('places') : '',
        ];
    }

    protected function addFilterConditions(array $filters, array &$where, array &$values): void
    {
        // Every word must appear somewhere in the title, in any order
        foreach (preg_split('/\s+/', $filters['search'], -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $where[] = 'tl_calendar_events.title LIKE ?';
            $values[] = '%'.addcslashes($word, '%_\\').'%';
        }

        if ($filters['eventGroup']) {
            $where[] = 'tl_calendar_events.eventGroup=?';
            $values[] = $filters['eventGroup'];
        }

        if (null !== ($from = $this->parseDate($filters['from']))) {
            $where[] = 'tl_calendar_events.startTime>=?';
            $values[] = $from;
        }

        if (null !== ($to = $this->parseDate($filters['to']))) {
            $where[] = 'tl_calendar_events.startTime<?';
            $values[] = strtotime('+1 day', $to);
        }
    }

    /**
     * The places filter needs the subscription counts, so it is applied to the built rows, then paged
     *
     * @return array{0: array, 1: int, 2: int} The entries of the page, the number of events and of subscriptions
     */
    protected function filterByPlaces(Result $events, string $places, int $limit, int $page, bool $skipWithoutSubscriptions): array
    {
        $rows = [];

        foreach ($this->createEntries($events, $skipWithoutSubscriptions) as $group) {
            foreach ($group['events'] as $event) {
                $full = $event['maxSubscriptions'] > 0 && $event['subscriptions'] >= $event['maxSubscriptions'];

                if (('full' === $places && !$full) || ('available' === $places && $full)) {
                    continue;
                }

                $rows[] = ['group' => $group['label'], 'event' => $event];
            }
        }

        $entries = [];

        foreach (\array_slice($rows, ($page - 1) * $limit, $limit) as $row) {
            $entries[$row['group']]['label'] = $row['group'];
            $entries[$row['group']]['events'][] = $row['event'];
        }

        return [$entries, \count($rows), array_sum(array_map(static fn ($row) => $row['event']['subscriptions'], $rows))];
    }

    /**
     * Event groups by calendar title, of one calendar or of all calendars with subscriptions
     *
     * @return array<string, array<int, string>>
     */
    protected function getEventGroupOptions(int $calendarId = 0): array
    {
        $options = [];
        $result = Database::getInstance()
            ->prepare('SELECT g.id, g.title, c.title AS calendarTitle FROM tl_calendar_event_group g JOIN tl_calendar c ON c.id=g.pid WHERE c.subscription_enable=1 AND (?=0 OR c.id=?) ORDER BY c.title, g.title')
            ->execute($calendarId, $calendarId);

        while ($result->next()) {
            $options[$result->calendarTitle][$result->id] = $result->title;
        }

        return $options;
    }

    protected function parseDate(string $value): int|null
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
     * Create the grouped event rows, using the calendar of each event
     *
     * @param bool $skipWithoutSubscriptions
     *
     * @return array
     */
    protected function createEntries(Result $events, $skipWithoutSubscriptions = true)
    {
        if (!$events->numRows) {
            return [];
        }

        $subscriptionFactory = Services::getSubscriptionFactory();
        $calendars = [];
        $entries = [];

        /** @var CalendarEventsModel $eventModel */
        foreach (Collection::createFromDbResult($events, 'tl_calendar_events') as $eventModel) {
            $subscriptionModels = SubscriptionModel::findBy('pid', $eventModel->id, ['order' => 'dateCreated']);

            if ($subscriptionModels === null && $skipWithoutSubscriptions) {
                continue;
            }

            $calendarModel = $calendars[$eventModel->pid] ??= CalendarModel::findByPk($eventModel->pid);

            $groupKey = date('Ym', $eventModel->startTime);

            if (!isset($entries[$groupKey])) {
                $entries[$groupKey] = [
                    'label' => Date::parse('F Y', $eventModel->startTime),
                    'events' => [],
                ];
            }

            $subscriptionsCount = 0;
            $waitingListCount = 0;

            /** @var SubscriptionModel $subscriptionModel */
            foreach ($subscriptionModels ?? [] as $subscriptionModel) {
                try {
                    $subscription = $subscriptionFactory->createFromModel($subscriptionModel);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }

                if ($subscription->isOnWaitingList()) {
                    $waitingListCount += $subscriptionModel->numberOfParticipants;
                } else {
                    $subscriptionsCount += $subscriptionModel->numberOfParticipants;
                }
            }

            $eventConfig = new EventConfig($calendarModel, $eventModel);
            $maximumSubscriptions = $eventConfig->getMaximumSubscriptions();

            $entries[$groupKey]['events'][] = [
                'id' => $eventModel->id,
                'title' => $eventModel->title,
                'calendar' => $calendarModel->title,
                'date' => Date::parse(Config::get($eventModel->addTime ?'datimFormat' : 'dateFormat'), $eventModel->startTime),
                'subscriptions' => $subscriptionsCount,
                'maxSubscriptions' => $maximumSubscriptions,
                'waitingList' => $eventConfig->hasWaitingList(),
                'waitingListLimit' => $eventConfig->getWaitingListLimit(),
                'waitingListSubscriptions' => ($maximumSubscriptions > 0) ? $waitingListCount : 0,
                'url' => sprintf(
                    'contao?do=calendar&table=tl_calendar_events_subscription&id=%s&rt=%s&ref=%s',
                    $eventModel->id,
                    Input::get('rt'),
                    Input::get('ref')
                ),
                'editUrl' => sprintf(
                    'contao?do=calendar&table=tl_calendar_events&act=edit&id=%s&rt=%s&ref=%s',
                    $eventModel->id,
                    Input::get('rt'),
                    Input::get('ref')
                ),
                'notificationsUrl' => sprintf(
                    'contao?do=calendar&table=tl_calendar_events&key=subscriptions_notification&id=%s&rt=%s&ref=%s',
                    $eventModel->id,
                    Input::get('rt'),
                    Input::get('ref')
                ),
            ];
        }

        return $entries;
    }
}
