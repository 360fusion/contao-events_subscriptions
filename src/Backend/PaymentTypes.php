<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory;
use Contao\Database;
use Contao\System;

/**
 * The ways to pay as tiles, under the on/off switch of Booking settings › Payments: each card provider, paying by invoice, and a
 * tile to add a provider. A provider says if it is active, or that its account details are missing.
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

        foreach ($db->query('SELECT * FROM tl_calendar_events_payment_provider ORDER BY title')->fetchAllAssoc() as $row) {
            $mode = ProviderFactory::mode($row);

            $tiles[] = [
                'title' => $row['title'],
                'description' => $P['typeRef'][$row['type']] ?? (string) $row['type'],
                'icon' => 'settings.svg',
                'href' => $router->generate('contao_backend', ['do' => 'event_payment_providers', 'act' => 'edit', 'id' => $row['id']]),
                'state' => match (true) {
                    !ProviderFactory::hasCredentials($row) => ['level' => 'warn', 'text' => $L['paymentTypes']['needsDetails']],
                    (bool) $row['published'] => ['level' => 'on', 'text' => $L['paymentTypes']['active']],
                    default => ['level' => 'off', 'text' => $L['paymentTypes']['inactive']],
                },
                'count' => implode(' · ', array_filter([$mode ? $L['providerMode'][$mode] : '', (string) ($row['currency'] ?: 'GBP')])),
            ];
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

        $tiles[] = [
            'title' => $L['paymentTypes']['addTitle'],
            'description' => $L['paymentTypes']['addText'],
            'icon' => 'new.svg',
            'href' => $router->generate('contao_backend', ['do' => 'event_payment_providers', 'act' => 'create', 'rt' => $container->get('contao.csrf.token_manager')->getDefaultTokenValue()]),
            'dashed' => true,
        ];

        return TileGrid::html($tiles);
    }
}
