<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\Retention;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Contao\Pagination;
use Contao\System;

/**
 * Events › Setup › Old booking data: the past events whose bookings still have personal data, to anonymise or delete
 */
class RetentionModule
{
    private const PER_PAGE = 100;

    public function __construct(mixed $dc = null)
    {
    }

    public function generate(): string
    {
        System::loadLanguageFile('tl_events_subscriptions_settings');

        $L = $GLOBALS['TL_LANG']['tl_events_subscriptions_settings'];
        $months = max(1, (int) Input::get('months') ?: Retention::months());
        $cutoff = strtotime(sprintf('-%d months', $months));
        $db = Database::getInstance();
        $from = "FROM tl_calendar_events e JOIN tl_calendar_events_subscription s ON s.pid=e.id AND s.lastname!='".Retention::ANONYMISED."' WHERE GREATEST(e.startTime, e.endTime)<?";

        if ('events_retention' === Input::post('FORM_SUBMIT')) {
            $action = 'anonymise' === Input::post('action') ? 'anonymise' : 'delete';

            // All matching events, or the ticked ones of this page
            $ids = Input::post('allMatching')
                ? $db->prepare("SELECT DISTINCT e.id $from")->execute($cutoff)->fetchEach('id')
                : array_map('intval', (array) Input::post('events'));

            $changed = (new Retention())->runForEvents($ids, $action);
            Message::addConfirmation(sprintf($L['retentionDone'][$action], number_format($changed), number_format(\count($ids))));
            Controller::reload();
        }

        $totals = $db->prepare("SELECT COUNT(DISTINCT e.id) AS events, COUNT(s.id) AS bookings $from")->execute($cutoff);
        $page = max(1, (int) Input::get('page'));

        $rows = $db->prepare("SELECT e.id, e.title, e.startTime, e.addTime, COUNT(s.id) AS bookings, (SELECT title FROM tl_calendar_event_group WHERE id=e.eventGroup) AS course, (SELECT title FROM tl_calendar WHERE id=e.pid) AS calendar $from GROUP BY e.id ORDER BY e.startTime DESC")
            ->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)
            ->execute($cutoff)
            ->fetchAllAssoc();

        foreach ($rows as &$row) {
            $row['date'] = Date::parse(Config::get($row['addTime'] ? 'datimFormat' : 'dateFormat'), $row['startTime']);
        }

        unset($row);

        $template = new BackendTemplate('be_events_subscriptions_retention');
        $template->months = $months;
        $template->rows = $rows;
        $template->events = (int) $totals->events;
        $template->bookings = (int) $totals->bookings;
        $template->action = Environment::get('request');
        $template->filterAction = strtok(Environment::get('requestUri'), '?');
        $template->defaultAction = 'anonymise' === Config::get('eventsSubscriptionsRetentionAction') ? 'anonymise' : 'delete';
        $template->pagination = (new Pagination((int) $totals->events, self::PER_PAGE, 7, 'page', new BackendTemplate('be_pagination'), true))->generate();
        $template->requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();

        return $template->parse();
    }
}
