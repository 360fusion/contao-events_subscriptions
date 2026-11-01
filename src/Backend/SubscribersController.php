<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\Pagination;
use Contao\System;

/**
 * Everyone booked on the events of a calendar, as one list with search and filters
 */
class SubscribersController extends OverviewController
{
    public function run()
    {
        // One calendar (row link), or all calendars with subscriptions (Events › Subscribers)
        $all = 'subscriptions_peopleAll' === Input::get('key') || 'event_subscribers' === Input::get('do');
        $calendar = $all ? null : CalendarModel::findByPk(Input::get('id'));

        if (!$all && (null === $calendar || !$calendar->subscription_enable)) {
            Controller::redirect('contao/main.php?act=error');
        }

        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_calendar_events');
        System::loadLanguageFile('tl_calendar_events_subscription');

        $filters = [
            'calendar' => $all ? (int) Input::get('calendar') : 0,
            'search' => trim((string) Input::get('search')),
            'eventGroup' => (int) Input::get('eventGroup'),
            'when' => \in_array(Input::get('when'), ['upcoming', 'past'], true) ? Input::get('when') : '',
            'attended' => \in_array(Input::get('attended'), ['yes', 'no'], true) ? Input::get('attended') : '',
            'payment' => \in_array(Input::get('payment'), PaymentStatus::ALL, true) ? Input::get('payment') : '',
        ];

        // Members' names and e-mail addresses are in tl_member
        $name = "IF(s.type='member', CONCAT_WS(' ', m.firstname, m.lastname), CONCAT_WS(' ', s.firstname, s.lastname))";
        $email = "IF(s.type='member', m.email, s.email)";
        $where = $all ? ['e.pid IN (SELECT id FROM tl_calendar WHERE subscription_enable=1)'] : ['e.pid=?'];
        $values = $all ? [] : [$calendar->id];

        if ($filters['calendar']) {
            $where[] = 'e.pid=?';
            $values[] = $filters['calendar'];
        }

        // Every word must appear in the name, e-mail address or event title
        foreach (preg_split('/\s+/', $filters['search'], -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $where[] = "CONCAT_WS(' ', $name, $email, e.title) LIKE ?";
            $values[] = '%'.addcslashes($word, '%_\\').'%';
        }

        if ($filters['eventGroup']) {
            $where[] = 'e.eventGroup=?';
            $values[] = $filters['eventGroup'];
        }

        if ('upcoming' === $filters['when']) {
            $where[] = 'GREATEST(e.startTime, e.endTime)>=UNIX_TIMESTAMP()';
        } elseif ('past' === $filters['when']) {
            $where[] = 'GREATEST(e.startTime, e.endTime)<UNIX_TIMESTAMP()';
        }

        if ($filters['attended']) {
            $where[] = 's.attended=?';
            $values[] = 'yes' === $filters['attended'] ? 1 : 0;
        }

        if ($filters['payment']) {
            $where[] = 's.paymentStatus=?';
            $values[] = $filters['payment'];
        }

        $db = Database::getInstance();
        $from = 'FROM tl_calendar_events_subscription s JOIN tl_calendar_events e ON e.id=s.pid LEFT JOIN tl_member m ON m.id=s.member WHERE '.implode(' AND ', $where);
        $totals = $db->prepare("SELECT COUNT(*) AS bookings, COALESCE(SUM(s.numberOfParticipants), 0) AS places $from")->execute(...$values);

        $limit = (int) Config::get('resultsPerPage');
        $page = max(1, (int) Input::get('esp'));
        $rows = $db->prepare("SELECT s.id, s.pid, s.dateCreated, s.numberOfParticipants, s.attended, s.paymentStatus, $name AS name, $email AS email, e.title AS eventTitle, e.startTime, e.addTime, (SELECT title FROM tl_calendar WHERE id=e.pid) AS calendarTitle $from ORDER BY e.startTime DESC, s.dateCreated")
            ->limit($limit, ($page - 1) * $limit)
            ->execute(...$values)
            ->fetchAllAssoc();

        $factory = Services::getSubscriptionFactory();
        $L = $GLOBALS['TL_LANG']['tl_calendar'];

        foreach ($rows as &$row) {
            try {
                $waiting = (bool) $factory->createFromModel(SubscriptionModel::findByPk($row['id']))->isOnWaitingList();
            } catch (\Throwable) {
                $waiting = false;
            }

            $row['eventDate'] = Date::parse(Config::get($row['addTime'] ? 'datimFormat' : 'dateFormat'), $row['startTime']);
            $row['booked'] = Date::parse(Config::get('datimFormat'), $row['dateCreated']);
            $row['status'] = $waiting ? $L['subscribers.waitingList'] : $L['subscribers.booked'];
            $row['payment'] = $row['paymentStatus'] ? ($GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'][$row['paymentStatus']] ?? $row['paymentStatus']) : '';
            $row['editUrl'] = sprintf('contao?do=calendar&table=tl_calendar_events_subscription&act=edit&id=%s&rt=%s', $row['id'], Input::get('rt'));
            $row['eventUrl'] = sprintf('contao?do=calendar&table=tl_calendar_events_subscription&id=%s&rt=%s', $row['pid'], Input::get('rt'));
        }

        unset($row);

        $template = new BackendTemplate('be_events_subscriptions_people');
        $template->backUrl = Backend::getReferer();
        $template->headline = $all ? $L['subscribers.headlineAll'] : sprintf($L['subscribers.headline'], $calendar->title);
        $template->showCalendar = $all;
        $template->calendarOptions = $all ? $db->query('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title')->fetchAllAssoc() : [];
        $template->rows = $rows;
        $template->summary = sprintf($L['subscribers.summary'], number_format((int) $totals->bookings), number_format((int) $totals->places));
        $template->pagination = (new Pagination((int) $totals->bookings, $limit, 7, 'esp', new BackendTemplate('be_pagination'), true))->generate();
        $template->filters = $filters;
        $template->filterAction = strtok(Environment::get('requestUri'), '?');
        $template->filterHidden = array_filter(['do' => Input::get('do'), 'key' => Input::get('key'), 'id' => $all ? null : Input::get('id'), 'rt' => Input::get('rt')], static fn ($value) => null !== $value);
        $template->eventGroupOptions = $this->getEventGroupOptions($all ? $filters['calendar'] : (int) $calendar->id);
        $template->paymentOptions = $db->query("SELECT DISTINCT paymentStatus FROM tl_calendar_events_subscription WHERE paymentStatus!=''")->fetchEach('paymentStatus');
        $template->exportUrl = $all ? '' : sprintf('contao?do=calendar&table=tl_calendar_events&key=subscriptions_exportCalendar&id=%s&rt=%s', $calendar->id, Input::get('rt'));

        return $template->parse();
    }
}
