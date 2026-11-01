<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\FrontendModule;

use Codefog\EventsSubscriptionsBundle\EventGroupFilter;
use Contao\BackendTemplate;
use Contao\Database;
use Contao\Date;
use Contao\Events;
use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;

/**
 * Calendars and their event groups as navigation or filter, linking to a list module with "Filter by URL".
 */
class EventNavigationModule extends Events
{
    protected $strTemplate = 'mod_event_navigation';

    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.$GLOBALS['TL_LANG']['FMD']['event_navigation'][0].' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = StringUtil::specialcharsUrl(System::getContainer()->get('router')->generate('contao_backend', ['do' => 'themes', 'table' => 'tl_module', 'act' => 'edit', 'id' => $this->id]));

            return $objTemplate->parse();
        }

        $this->cal_calendar = $this->sortOutProtected($this->cal_allCalendars ? Database::getInstance()->query('SELECT id FROM tl_calendar')->fetchEach('id') : StringUtil::deserialize($this->cal_calendar, true));

        if (empty($this->cal_calendar)) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        $db = Database::getInstance();
        $ids = implode(',', array_map('intval', $this->cal_calendar));
        $calendars = $db->query("SELECT id, title, alias FROM tl_calendar WHERE id IN ($ids) ORDER BY title")->fetchAllAssoc();
        $groups = $db->query("SELECT id, pid, title, alias FROM tl_calendar_event_group WHERE pid IN ($ids) AND published=1 ORDER BY title")->fetchAllAssoc();

        $counts = $this->countUpcoming();
        $activeCalendar = EventGroupFilter::resolve(Input::get('calendar'), EventGroupFilter::idsByAlias($calendars));
        $activeGroup = EventGroupFilter::resolve(Input::get('group'), EventGroupFilter::idsByAlias($groups));
        $baseUrl = $this->getBaseUrl();
        $items = [];

        foreach ($calendars as $calendar) {
            $children = [];

            foreach ($groups as $group) {
                $count = $counts['group'][$group['id']] ?? 0;

                if ((int) $group['pid'] !== (int) $calendar['id'] || ($this->cal_hideEmptyGroups && 0 === $count)) {
                    continue;
                }

                $children[] = [
                    'id' => $group['id'],
                    'value' => $group['alias'] ?: $group['id'],
                    'title' => $group['title'],
                    'href' => $baseUrl.'?'.http_build_query(['group' => $group['alias'] ?: $group['id']]),
                    'count' => $count,
                    'active' => $activeGroup === (int) $group['id'],
                ];
            }

            $count = $counts['calendar'][$calendar['id']] ?? 0;

            if ($this->cal_hideEmptyGroups && 0 === $count) {
                continue;
            }

            $items[] = [
                'id' => $calendar['id'],
                'value' => $calendar['alias'] ?: $calendar['id'],
                'title' => $calendar['title'],
                'href' => $baseUrl.'?'.http_build_query(['calendar' => $calendar['alias'] ?: $calendar['id']]),
                'count' => $count,
                'active' => $activeCalendar === (int) $calendar['id'] || (bool) array_filter($children, static fn ($child) => $child['active']),
                'groups' => $children,
            ];
        }

        // The next twelve months for the filter template
        $months = [];

        for ($i = 0; $i < 12; ++$i) {
            $time = strtotime(date('Y-m-01').' +'.$i.' month');
            $months[date('Ym', $time)] = Date::parse('F Y', $time);
        }

        $this->Template->items = $items;
        $this->Template->months = $months;
        $this->Template->showCounts = (bool) $this->cal_showCounts;
        $this->Template->action = $baseUrl;
        $this->Template->resetHref = $baseUrl;
        $this->Template->isFiltered = null !== $activeCalendar || null !== $activeGroup || '' !== (string) Input::get('month');
        $this->Template->current = [
            'calendar' => (string) Input::get('calendar'),
            'group' => (string) Input::get('group'),
            'month' => (string) Input::get('month'),
        ];
    }

    /**
     * Count the upcoming event dates per calendar and per event group, the way the event list sees them
     *
     * @return array{calendar: array<int, int>, group: array<int, int>}
     */
    private function countUpcoming(): array
    {
        $counts = ['calendar' => [], 'group' => []];

        // No module is passed, so the getAllEvents hooks (and their per-event queries) are skipped
        $allEvents = System::getContainer()->get('contao_calendar.generator.calendar_events')->getAllEvents(
            $this->cal_calendar,
            new \DateTime(),
            (new \DateTime())->setTimestamp(min(4294967295, PHP_INT_MAX)),
            null,
            true,
        );

        foreach ($allEvents as $times) {
            foreach ($times as $events) {
                foreach ($events as $event) {
                    $counts['calendar'][$event['pid']] = ($counts['calendar'][$event['pid']] ?? 0) + 1;

                    if ($event['eventGroup'] ?? 0) {
                        $counts['group'][$event['eventGroup']] = ($counts['group'][$event['eventGroup']] ?? 0) + 1;
                    }
                }
            }
        }

        return $counts;
    }

    private function getBaseUrl(): string
    {
        $page = ($this->jumpTo ? PageModel::findPublishedById($this->jumpTo) : null) ?? $GLOBALS['objPage'] ?? null;

        return $page ? System::getContainer()->get('contao.routing.content_url_generator')->generate($page) : '';
    }
}
