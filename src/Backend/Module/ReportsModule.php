<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Report;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Symfony\Component\HttpFoundation\Response;

/**
 * Events › Reports: fill rate, cancellations, did-not-attend and feedback for each course and month, and the people who keep missing events
 */
class ReportsModule
{
    private const MAX_NO_SHOWS = 100;

    public function __construct(mixed $dc = null)
    {
    }

    public function generate(): string
    {
        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_events_subscriptions_settings');

        $L = $GLOBALS['TL_LANG']['tl_calendar'];
        $from = $this->month(Input::get('from'), date('Y-m', strtotime('first day of -11 months')));
        $to = max($from, $this->month(Input::get('to'), date('Y-m')));
        $calendar = (int) Input::get('calendar');
        $events = Report::events(strtotime($from.'-01'), strtotime($to.'-01 +1 month') - 1, $calendar);
        $rows = Report::aggregate($events);
        $attendance = Features::attendance();

        if (Input::get('export')) {
            $this->download($rows, $attendance, $from, $to);
        }

        $token = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();
        $noShows = [];

        if ($attendance) {
            $limit = Features::noShowLimit();
            $people = array_filter(Report::missedCounts(), static fn (array $person) => $person['missed'] >= $limit);
            uasort($people, static fn (array $a, array $b) => [$b['missed'], $b['last']] <=> [$a['missed'], $a['last']]);

            foreach (\array_slice($people, 0, self::MAX_NO_SHOWS, true) as $email => $person) {
                $noShows[] = $person + ['email' => $email, 'lastDate' => Date::parse(Config::get('dateFormat'), $person['last']), 'url' => sprintf('contao?do=calendar&key=subscriptions_person&id=%d&rt=%s', $person['booking'], $token)];
            }
        }

        $template = new BackendTemplate('be_events_subscriptions_reports');
        $template->from = $from;
        $template->to = $to;
        $template->calendar = $calendar;
        $template->calendarOptions = Database::getInstance()->query('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title')->fetchAllAssoc();
        $template->rows = $rows;
        $template->totals = Report::totals($events);
        $template->attendance = $attendance;
        $template->noShows = $noShows;
        $template->noShowLimit = Features::noShowLimit();
        $template->noShowCount = \count($noShows);
        $template->filterAction = strtok(Environment::get('requestUri'), '?');
        $template->exportUrl = '?'.http_build_query(['do' => 'event_reports', 'from' => $from, 'to' => $to, 'calendar' => $calendar ?: null, 'export' => 1, 'rt' => $token]);

        return $template->parse();
    }

    private function month(mixed $value, string $default): string
    {
        return \is_string($value) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) ? $value : $default;
    }

    private function download(array $rows, bool $attendance, string $from, string $to): never
    {
        $L = $GLOBALS['TL_LANG']['tl_calendar'];
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF"); // so Excel reads it as UTF-8

        $header = [$L['reports.month'], $L['reports.course'], $L['reports.calendar'], $L['reports.events'], $L['reports.places'], $L['reports.booked'], $L['reports.fill'], $L['reports.waiting']];

        if ($attendance) {
            array_push($header, $L['reports.attended'], $L['reports.missed'], $L['reports.missedRate']);
        }

        array_push($header, $L['reports.cancelled'], $L['reports.rating'], $L['reports.ratingCount']);
        fputcsv($out, $header);

        foreach ($rows as $row) {
            $line = [$row['month'], $row['course'], $row['calendar'], $row['events'], $row['capacity'] ?: '', $row['booked'], null === $row['fill'] ? '' : $row['fill'].'%', $row['waiting']];

            if ($attendance) {
                array_push($line, $row['attended'], $row['missed'], null === $row['missedRate'] ? '' : $row['missedRate'].'%');
            }

            array_push($line, $row['cancelled'], $row['rating'] ?? '', $row['ratingCount']);
            fputcsv($out, $line);
        }

        rewind($out);

        throw new ResponseException(new Response(stream_get_contents($out), 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => sprintf('attachment; filename="event-report-%s-to-%s.csv"', $from, $to),
        ]));
    }
}
