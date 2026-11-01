<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['event_list_subscribe'] = [
    'Eventliste mit Anmeldung',
    'Fügt der Seite eine Eventliste mit Anmeldemöglichkeit hinzu.',
];
$GLOBALS['TL_LANG']['FMD']['event_reader_subscribe'] = [
    'Eventleser mit Anmeldung',
    'Fügt der Seite einen Eventleser mit Anmeldemöglichkeit hinzu.',
];
$GLOBALS['TL_LANG']['FMD']['event_subscribe']      = [
    'Anmeldeformular für Events',
    'Fügt der Seite ein Anmeldeformular hinzu.',
];
$GLOBALS['TL_LANG']['FMD']['event_subscriptions']    = [
    'Mitglied Anmeldeliste',
    'Fügt eine Liste aller Events hinzu, zu denen das aktuelle Mitglied angemeldet ist.',
];
$GLOBALS['TL_LANG']['FMD']['event_navigation']     = [
    'Veranstaltungsnavigation',
    'Listet die Kalender und ihre Veranstaltungsgruppen als Navigation oder Filter auf, mit Links zu einer Veranstaltungsliste mit "Nach URL filtern".',
];

/**
 * Backend-Menü: die Gruppe "Veranstaltungen"
 */
$GLOBALS['TL_LANG']['MOD']['events_subscriptions'] = 'Veranstaltungen';
$GLOBALS['TL_LANG']['MOD']['calendar'] = ['Kalender', 'Die Kalender, ihre Veranstaltungen und Anmeldungen verwalten'];
$GLOBALS['TL_LANG']['MOD']['event_upcoming'] = ['Kommende Veranstaltungen', 'Die kommenden Veranstaltungen aller Kalender und ihre Anmeldungen'];
$GLOBALS['TL_LANG']['MOD']['event_subscribers'] = ['Teilnehmer', 'Alle Anmeldungen der Veranstaltungen aller Kalender'];
$GLOBALS['TL_LANG']['MOD']['event_emails'] = ['E-Mails', 'Die E-Mails zu den Anmeldungen'];
$GLOBALS['TL_LANG']['MOD']['event_payments'] = ['Zahlungen', 'Zahlungen, Erstattungen und Rechnungen der Anmeldungen'];
$GLOBALS['TL_LANG']['MOD']['event_setup'] = ['Einrichtung', 'Anmeldefelder, Zahlungsanbieter und weitere Anmeldeeinstellungen'];
$GLOBALS['TL_LANG']['MOD']['event_fields'] = ['Anmeldefelder', 'Die zusätzlichen Felder des Gast-Anmeldeformulars, gewählt pro Kalender, Veranstaltungsgruppe oder Veranstaltung'];
$GLOBALS['TL_LANG']['MOD']['event_payment_providers'] = ['Zahlungsanbieter', 'Die Konten für Kartenzahlungen: Stripe, PayPal, Mollie oder GOV.UK Pay'];
