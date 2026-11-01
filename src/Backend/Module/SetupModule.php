<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer;
use Codefog\EventsSubscriptionsBundle\Features;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\System;

/**
 * Events › Setup: one page with a tile per settings area
 */
class SetupModule
{
    public function __construct(mixed $dc = null)
    {
    }

    public function generate(): string
    {
        System::loadLanguageFile('tl_events_subscriptions_settings');

        $db = Database::getInstance();
        $security = System::getContainer()->get('security.helper');
        $router = System::getContainer()->get('router');
        $L = $GLOBALS['TL_LANG']['MOD'];
        $S = $GLOBALS['TL_LANG']['tl_events_subscriptions_settings'];
        $can = static fn (string $module) => $security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, $module);
        $on = static fn (bool $value) => $S['setupStatus'][$value ? 'on' : 'off'];
        $tiles = [];
        $settings = [];

        // Booking settings: one page for each area
        $status = [
            'event_settings_payments' => $on(Features::payments()),
            'event_settings_certificates' => $on(Features::certificates()),
            'event_settings_mailings' => sprintf($S['setupStatus']['mailings'], \count(array_filter([Config::get('eventsSubscriptionsTrainerNotification'), Config::get('eventsSubscriptionsFollowUpNotification')]))),
        ];

        foreach (SettingsContainer::PAGES as $module => $palette) {
            if ($can($module)) {
                $settings[] = ['title' => $S['sections'][$module], 'description' => $L[$module][1], 'icon' => 'settings.svg', 'count' => $status[$module] ?? '', 'href' => $router->generate('contao_backend', ['do' => $module])];
            }
        }

        $areas = [
            'event_health' => ['icon' => 'important.svg'],
            'event_retention' => ['icon' => 'delete.svg'],
            'event_fields' => ['table' => 'tl_calendar_events_subscription_field', 'icon' => 'modules.svg', 'count' => 'SELECT COUNT(*) AS n FROM tl_calendar_events_subscription_field WHERE disabled=0'],
            'event_payment_providers' => ['table' => 'tl_calendar_events_payment_provider', 'icon' => 'settings.svg', 'count' => 'SELECT COUNT(*) AS n FROM tl_calendar_events_payment_provider'],
        ];

        foreach ($areas as $module => $area) {
            if (!$can($module) || ('event_payment_providers' === $module && !Features::payments())) {
                continue;
            }

            $tiles[] = [
                'title' => $L[$module][0],
                'description' => $L[$module][1],
                'icon' => $area['icon'],
                'count' => match ($module) {
                    // Past events whose bookings still have personal data
                    'event_retention' => (int) $db->prepare("SELECT COUNT(DISTINCT e.id) AS n FROM tl_calendar_events e JOIN tl_calendar_events_subscription s ON s.pid=e.id AND s.lastname!=? WHERE GREATEST(e.startTime, e.endTime)<?")->execute(\Codefog\EventsSubscriptionsBundle\Retention::ANONYMISED, strtotime(sprintf('-%d months', \Codefog\EventsSubscriptionsBundle\Retention::months())))->n,
                    // Number of checks that need attention
                    'event_health' => \count(array_filter(HealthModule::getChecks(), static fn ($check) => $check['count'] > 0)),
                    default => (int) $db->query($area['count'])->n,
                },
                'href' => $router->generate('contao_backend', ['do' => $module]),
            ];
        }

        // Calendar groups are switched on in System › Settings
        if (Features::calendarGroups() && $security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, 'calendar')) {
            $tiles[] = [
                'title' => $GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'][0],
                'description' => $GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'][1],
                'icon' => 'folderC.svg',
                'count' => (int) $db->query('SELECT COUNT(*) AS n FROM tl_calendar_group')->n,
                'href' => $router->generate('contao_backend', ['do' => 'calendar', 'table' => 'tl_calendar_group']),
            ];
        }

        $template = new BackendTemplate('be_events_subscriptions_setup');
        $template->sections = array_values(array_filter([
            ['title' => $S['setupSections']['settings'], 'tiles' => $settings],
            ['title' => $S['setupSections']['tools'], 'tiles' => $tiles],
        ], static fn (array $section) => $section['tiles']));

        return $template->parse();
    }
}
