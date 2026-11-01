<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Attendees;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\BackendTemplate;
use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Database;
use Contao\Date;
use Contao\Input;
use Contao\System;
use Symfony\Component\HttpFoundation\Response;

/**
 * The printable sign-in sheet of an event (a row operation of the event list). A page of its own, so it prints without the back end around it.
 */
class RegisterController
{
    public function run(): never
    {
        System::loadLanguageFile('tl_calendar');

        $event = CalendarEventsModel::findByPk((int) Input::get('id'));
        $user = BackendUser::getInstance();

        if (null === $event || !($user->isAdmin || $user->hasAccess($event->pid, 'calendars'))) {
            Controller::redirect('contao/main.php?act=error');
        }

        $attendees = Attendees::forEvent(Services::getEventConfigFactory()->create((int) $event->id));
        $sort = static fn (array $a, array $b) => strnatcasecmp($a['name'], $b['name']);
        $booked = array_values(array_filter($attendees, static fn (array $row) => !$row['waiting']));
        $waiting = array_values(array_filter($attendees, static fn (array $row) => $row['waiting']));
        usort($booked, $sort);
        usort($waiting, $sort);

        $template = new BackendTemplate('be_events_subscriptions_register');
        $template->title = $event->title;
        $template->course = $event->eventGroup ? Database::getInstance()->prepare('SELECT title FROM tl_calendar_event_group WHERE id=?')->execute($event->eventGroup)->title : '';
        $template->when = Date::parse(Config::get($event->addTime ? 'datimFormat' : 'dateFormat'), $event->startTime).($event->addTime && $event->endTime > $event->startTime ? ' – '.Date::parse(Config::get('timeFormat'), $event->endTime) : '');
        $template->location = (string) $event->location;
        $template->booked = $booked;
        $template->waiting = $waiting;
        $template->places = array_sum(array_column($booked, 'places'));
        $template->showAnswers = (bool) Input::get('answers');
        $template->toggleUrl = '?'.http_build_query(array_filter(['do' => 'calendar', 'key' => 'subscriptions_register', 'id' => $event->id, 'rt' => Input::get('rt'), 'answers' => Input::get('answers') ? null : 1]));

        throw new ResponseException(new Response($template->parse()));
    }
}
