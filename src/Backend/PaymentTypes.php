<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory;
use Contao\Database;
use Contao\System;

/**
 * The ways to pay as tiles, under the on/off switch of Booking settings › Payments: every kind of card provider (Stripe, PayPal, Mollie,
 * GOV.UK Pay) and paying by invoice. A provider that is not set up, or has no account details, is shown inactive.
 */
final class PaymentTypes
{
    public static function html(): string
    {
        System::loadLanguageFile('tl_calendar_events_payment_provider');
        System::loadLanguageFile('tl_events_subscriptions_settings');

        $L = $GLOBALS['TL_LANG']['tl_events_subscriptions_settings'];
        $P = $GLOBALS['TL_LANG']['tl_calendar_events_payment_provider'];
        $container = System::getContainer();
        $router = $container->get('router');
        $db = Database::getInstance();
        $tiles = [];

        $rows = $db->query('SELECT * FROM tl_calendar_events_payment_provider WHERE tstamp>0 ORDER BY title')->fetchAllAssoc();
        $token = $container->get('contao.csrf.token_manager')->getDefaultTokenValue();
        $inactive = ['level' => 'off', 'text' => $L['paymentTypes']['inactive']];

        // Every kind of provider is listed. One that is set up shows its state; one that is not stays inactive and opens a new form of its kind.
        foreach (array_keys(ProviderFactory::TYPES) as $type) {
            $own = array_filter($rows, static fn (array $row) => $type === $row['type']);

            if (!$own) {
                $tiles[] = [
                    'title' => $P['typeRef'][$type],
                    'description' => $P['typeText'][$type],
                    'icon' => 'settings.svg',
                    'href' => $router->generate('contao_backend', ['do' => 'event_payment_providers', 'act' => 'create', 'type' => $type, 'rt' => $token]),
                    'state' => $inactive,
                    'count' => $L['paymentTypes']['notSetUp'],
                ];

                continue;
            }

            foreach ($own as $row) {
                $mode = ProviderFactory::mode($row);
                $complete = ProviderFactory::hasCredentials($row);

                $tiles[] = [
                    'title' => $row['title'],
                    'description' => $P['typeText'][$type],
                    'icon' => 'settings.svg',
                    'href' => $router->generate('contao_backend', ['do' => 'event_payment_providers', 'act' => 'edit', 'id' => $row['id']]),
                    'state' => $complete && $row['published'] ? ['level' => 'on', 'text' => $L['paymentTypes']['active']] : $inactive,
                    'count' => $complete ? implode(' · ', array_filter([$mode ? $L['providerMode'][$mode] : '', (string) ($row['currency'] ?: 'GBP')])) : $L['paymentTypes']['detailsMissing'],
                ];
            }
        }

        $invoices = (int) $db->query("SELECT COUNT(*) AS n FROM tl_calendar_events_payment WHERE method='invoice'")->n;
        $tiles[] = [
            'title' => $L['paymentTypes']['invoiceTitle'],
            'description' => $L['paymentTypes']['invoiceText'],
            'icon' => 'tablewizard.svg',
            'href' => $router->generate('contao_backend', ['do' => 'event_settings_invoice']),
            'state' => Features::invoice() ? ['level' => 'on', 'text' => $L['paymentTypes']['active']] : ['level' => 'off', 'text' => $L['paymentTypes']['inactive']],
            'count' => sprintf($L['setupUnits']['invoices'][1 === $invoices ? 0 : 1], number_format($invoices)),
        ];

        return TileGrid::html($tiles);
    }
}
