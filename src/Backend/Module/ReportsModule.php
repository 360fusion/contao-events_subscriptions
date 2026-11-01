<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Backend\TableExport;
use Codefog\EventsSubscriptionsBundle\Report;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\System;

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

        $calendarTitle = '';

        foreach (Database::getInstance()->prepare('SELECT title FROM tl_calendar WHERE id=?')->execute($calendar) as $row) {
            $calendarTitle = $row['title'];
        }

        if (Input::get('export')) {
            $this->download(TableExport::format(Input::get('export')), $rows, Report::totals($events), $attendance, $from, $to, $calendarTitle);
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
        $template->exportUrls = array_combine(TableExport::FORMATS, array_map(static fn (string $format) => '?'.http_build_query(['do' => 'event_reports', 'from' => $from, 'to' => $to, 'calendar' => $calendar ?: null, 'export' => $format, 'rt' => $token]), TableExport::FORMATS));

        return $template->parse();
    }

    private function month(mixed $value, string $default): string
    {
        return \is_string($value) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) ? $value : $default;
    }

    private function download(string $format, array $rows, array $totals, bool $attendance, string $from, string $to, string $calendarTitle): never
    {
        $L = $GLOBALS['TL_LANG']['tl_calendar'];
        $header = [$L['reports.month'], $L['reports.course'], $L['reports.calendar'], $L['reports.events'], $L['reports.places'], $L['reports.booked'], $L['reports.fill'].' (%)', $L['reports.waiting']];

        if ($attendance) {
            array_push($header, $L['reports.attended'], $L['reports.missed'], $L['reports.missedRate']);
        }

        array_push($header, $L['reports.cancelled'], $L['reports.rating'], $L['reports.ratingCount']);

        // Numbers stay numbers in the file; what is not known is left empty
        $line = static function (array $row, string $month, string $course, string $calendar) use ($attendance): array {
            $line = [$month, $course, $calendar, $row['events'], $row['capacity'] ?: '', $row['booked'], $row['fill'] ?? '', $row['waiting']];

            if ($attendance) {
                array_push($line, null === $row['missedRate'] ? '' : $row['attended'], null === $row['missedRate'] ? '' : $row['missed'], $row['missedRate'] ?? '');
            }

            return array_merge($line, [$row['cancelled'], $row['rating'] ?? '', $row['ratingCount']]);
        };

        $lines = array_map(static fn (array $row) => $line($row, $row['month'], $row['course'], $row['calendar']), $rows);
        $lines[] = $line($totals, $L['reports.total'], '', '');

        TableExport::send(
            $format,
            sprintf('event-report-%s-to-%s', $from, $to),
            $GLOBALS['TL_LANG']['MOD']['event_reports'][0],
            $header,
            $lines,
            array_filter([sprintf($L['reports.period'], $from, $to), $calendarTitle ? $L['reports.calendar'].': '.$calendarTitle : '', $L['reports.help'], $L['reports.notes']]),
            true,
        );
    }
}
