<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

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
        $db = Database::getInstance();
        $security = System::getContainer()->get('security.helper');
        $router = System::getContainer()->get('router');
        $L = $GLOBALS['TL_LANG']['MOD'];
        $tiles = [];

        $areas = [
            'event_settings' => ['icon' => 'settings.svg'],
            'event_health' => ['icon' => 'important.svg'],
            'event_fields' => ['table' => 'tl_calendar_events_subscription_field', 'icon' => 'modules.svg', 'count' => 'SELECT COUNT(*) AS n FROM tl_calendar_events_subscription_field WHERE disabled=0'],
            'event_payment_providers' => ['table' => 'tl_calendar_events_payment_provider', 'icon' => 'settings.svg', 'count' => 'SELECT COUNT(*) AS n FROM tl_calendar_events_payment_provider'],
        ];

        foreach ($areas as $module => $area) {
            if (!$security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, $module)) {
                continue;
            }

            $tiles[] = [
                'title' => $L[$module][0],
                'description' => $L[$module][1],
                'icon' => $area['icon'],
                'count' => match ($module) {
                    'event_settings' => '',
                    // Number of checks that need attention
                    'event_health' => \count(array_filter(HealthModule::getChecks(), static fn ($check) => $check['count'] > 0)),
                    default => (int) $db->query($area['count'])->n,
                },
                'href' => $router->generate('contao_backend', ['do' => $module]),
            ];
        }

        // Calendar groups are switched on in System › Settings
        if (Config::get('eventsSubscriptionsCalendarGroups') && $security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, 'calendar')) {
            $tiles[] = [
                'title' => $GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'][0],
                'description' => $GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'][1],
                'icon' => 'folderC.svg',
                'count' => (int) $db->query('SELECT COUNT(*) AS n FROM tl_calendar_group')->n,
                'href' => $router->generate('contao_backend', ['do' => 'calendar', 'table' => 'tl_calendar_group']),
            ];
        }

        $template = new BackendTemplate('be_events_subscriptions_setup');
        $template->tiles = $tiles;

        return $template->parse();
    }
}
