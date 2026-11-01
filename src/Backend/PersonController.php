<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Report;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\Input;
use Contao\System;

/**
 * Everything one person booked, from the name in the subscribers list. The booking ID opens it, so no e-mail address is in the address bar.
 */
class PersonController
{
    public function run(): string
    {
        System::loadLanguageFile('tl_calendar');

        $L = $GLOBALS['TL_LANG']['tl_calendar'];
        $person = Report::person((int) Input::get('id'));
        $user = BackendUser::getInstance();

        if (null === $person) {
            Controller::redirect('contao/main.php?act=error');
        }

        // Only the calendars this user may see
        $rows = array_values(array_filter($person['rows'], static fn (array $row) => $user->isAdmin || $user->hasAccess($row['pid'], 'calendars')));
        $dateFormat = static fn (array $row) => Date::parse(Config::get($row['addTime'] ? 'datimFormat' : 'dateFormat'), $row['startTime']);

        foreach ($rows as &$row) {
            $row['date'] = $dateFormat($row);
            $row['outcomeLabel'] = $L['person.outcome.'.$row['outcome']];
            $row['paymentLabel'] = $row['paymentStatus'] ? ($GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'][$row['paymentStatus']] ?? $row['paymentStatus']) : '';
        }

        unset($row);

        if (Input::get('export')) {
            $this->download(TableExport::format(Input::get('export')), $rows, $person['name'], $person['email']);
        }

        $count = static fn (string $outcome) => \count(array_filter($rows, static fn (array $row) => $outcome === $row['outcome']));
        $token = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();
        $template = new BackendTemplate('be_events_subscriptions_person');
        $template->backUrl = Backend::getReferer();
        $template->name = $person['name'];
        $template->email = $person['email'];
        $template->rows = $rows;
        $template->attendance = Features::attendance();
        $template->summary = sprintf($L['person.summary'], \count($rows), $count('attended'), $count('missed'));
        $template->flag = $count('missed') >= Features::noShowLimit() ? sprintf($L['person.flag'], $count('missed')) : '';
        $template->exportUrls = array_combine(TableExport::FORMATS, array_map(static fn (string $format) => '?'.http_build_query(['do' => 'calendar', 'key' => 'subscriptions_person', 'id' => Input::get('id'), 'export' => $format, 'rt' => $token]), TableExport::FORMATS));

        return $template->parse();
    }

    private function download(string $format, array $rows, string $name, string $email): never
    {
        $L = $GLOBALS['TL_LANG']['tl_calendar'];

        TableExport::send(
            $format,
            'bookings-'.$name,
            $name,
            [$L['person.date'], $L['person.course'], $L['person.event'], $L['person.calendar'], $L['person.outcome'], $L['person.payment']],
            array_map(static fn (array $row) => [$row['date'], (string) $row['course'], $row['title'], $row['calendar'], $row['outcomeLabel'], $row['paymentLabel']], $rows),
            array_filter([$email]),
        );
    }
}
