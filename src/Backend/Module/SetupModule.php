<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer;
use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Retention;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\System;

/**
 * Events › Settings: one page with a tile per settings area, the e-mail templates and the reports
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
        $count = static fn (string $table, string $where = '1') => (int) $db->query("SELECT COUNT(*) AS n FROM $table WHERE $where")->n;
        $url = static fn (array $query) => $router->generate('contao_backend', $query);

        // The bar along the bottom of a tile: the state on the left (on, off, fine, needs attention), a number on the right
        $unit = static fn (string $key, int $number): string => sprintf($S['setupUnits'][$key][1 === $number ? 0 : 1], number_format($number));
        $state = static fn (string $level, string $text): array => ['level' => $level, 'text' => $text];
        $onOff = static fn (bool $on): array => $state($on ? 'on' : 'off', $S['setupBar'][$on ? 'on' : 'off']);
        $tile = static fn (string $title, string $description, string $icon, string $href, array|null $state = null, string $count = ''): array => compact('title', 'description', 'icon', 'href', 'state', 'count');
        $tiles = [];
        $settings = [];

        // Booking settings: one page for each area
        foreach (SettingsContainer::PAGES as $module => $palette) {
            if ($can($module)) {
                $settings[] = match ($module) {
                    'event_settings' => $tile($S['sections'][$module], $L[$module][1], 'settings.svg', $url(['do' => $module]), null, sprintf($S['setupUnits']['features'][1], \count(array_filter([Features::attendance(), Features::calendarGroups(), Features::oneBookingPerCourse(), Features::venues(), Features::trainers()])))),
                    'event_settings_payments' => $tile($S['sections'][$module], $L[$module][1], 'settings.svg', $url(['do' => $module]), $onOff(Features::payments()), Features::payments() ? $unit('payments', $count('tl_calendar_events_payment')) : ''),
                    'event_settings_emails' => $tile($S['sections'][$module], $L[$module][1], 'settings.svg', $url(['do' => $module]), '' !== trim(Features::text('eventsSubscriptionsSignature')) ? $state('on', $S['setupBar']['signatureOn']) : $state('off', $S['setupBar']['signatureOff'])),
                    'event_settings_certificates' => $tile($S['sections'][$module], $L[$module][1], 'settings.svg', $url(['do' => $module]), $onOff(Features::certificates()), Features::certificates() ? $unit('attended', $count('tl_calendar_events_subscription', 'attended=1')) : ''),
                    'event_settings_mailings' => $tile($S['sections'][$module], $L[$module][1], 'settings.svg', $url(['do' => $module]), $onOff(($chosen = \count(array_filter([Config::get('eventsSubscriptionsTrainerNotification'), Config::get('eventsSubscriptionsFollowUpNotification')]))) > 0), sprintf($S['setupUnits']['chosen'][1], $chosen)),
                };
            }

            // The payment providers, once payments are on, right after Payments
            if ('event_settings_payments' === $module && Features::payments() && $can('event_payment_providers')) {
                $settings[] = $tile($L['event_payment_providers'][0], $L['event_payment_providers'][1], 'settings.svg', $url(['do' => 'event_payment_providers']), null, $unit('providers', $count('tl_calendar_events_payment_provider')));
            }

            // The registration fields are a list of their own, so a tile rather than a page of the form; right after General
            if ('event_settings' === $module && $can('event_fields')) {
                $settings[] = $tile($L['event_fields'][0], $L['event_fields'][1], 'modules.svg', $url(['do' => 'event_fields']), null, $unit('fields', $count('tl_calendar_events_subscription_field', 'disabled=0')));
            }

            // The lists of places and trainers, once switched on in General, are managed from here too
            if ('event_settings' === $module) {
                foreach (['event_venues' => [Features::venues(), 'tl_calendar_events_venue', 'show.svg', 'venues'], 'event_trainers' => [Features::trainers(), 'tl_calendar_events_trainer', 'member.svg', 'trainers']] as $list => [$enabled, $table, $icon, $noun]) {
                    if ($enabled && $can($list)) {
                        $settings[] = $tile($L[$list][0], $L[$list][1], $icon, $url(['do' => $list]), $onOff(true), $unit($noun, $count($table)));
                    }
                }
            }
        }

        if ($can('event_emails')) {
            $tiles[] = $tile($L['event_emails'][0], $L['event_emails'][1], 'mover.svg', $url(['do' => 'event_emails']), null, $unit('emails', $count('tl_nc_notification', "type LIKE 'events_subscription%'")));
        }

        if ($can('event_reports')) {
            $tiles[] = $tile($L['event_reports'][0], $L['event_reports'][1], 'show.svg', $url(['do' => 'event_reports']));
        }

        if ($can('event_health')) {
            $checks = \count(array_filter(HealthModule::getChecks(), static fn ($check) => $check['count'] > 0));
            $tiles[] = $tile($L['event_health'][0], $L['event_health'][1], 'important.svg', $url(['do' => 'event_health']), $checks ? $state('warn', $S['setupBar']['attention']) : $state('on', $S['setupBar']['fine']), $unit('checks', $checks));
        }

        if ($can('event_retention')) {
            // Past events whose bookings still have personal data
            $old = (int) $db->prepare('SELECT COUNT(DISTINCT e.id) AS n FROM tl_calendar_events e JOIN tl_calendar_events_subscription s ON s.pid=e.id AND s.lastname!=? WHERE GREATEST(e.startTime, e.endTime)<?')->execute(Retention::ANONYMISED, strtotime(sprintf('-%d months', Retention::months())))->n;
            $auto = null !== Retention::cutoff();
            $tiles[] = $tile($L['event_retention'][0], $L['event_retention'][1], 'delete.svg', $url(['do' => 'event_retention']), $state($auto ? 'on' : 'off', $S['setupBar'][$auto ? 'autoOn' : 'autoOff']), $unit('events', $old));
        }

        // Calendar groups are switched on in System › Settings
        if (Features::calendarGroups() && $can('calendar')) {
            $tiles[] = $tile($GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'][0], $GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'][1], 'folderC.svg', $url(['do' => 'calendar', 'table' => 'tl_calendar_group']), null, $unit('groups', $count('tl_calendar_group')));
        }

        $template = new BackendTemplate('be_events_subscriptions_setup');
        $template->sections = array_values(array_filter([
            ['title' => $S['setupSections']['settings'], 'tiles' => $settings],
            ['title' => $S['setupSections']['tools'], 'tiles' => $tiles],
        ], static fn (array $section) => $section['tiles']));

        return $template->parse();
    }
}
