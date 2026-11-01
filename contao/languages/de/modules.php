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
$GLOBALS['TL_LANG']['MOD']['event_emails'] = ['E-Mail-Vorlagen', 'Die E-Mails zu Anmeldungen, Erinnerungen und Dankeschön schreiben und ändern'];
$GLOBALS['TL_LANG']['MOD']['event_payments'] = ['Zahlungen', 'Zahlungen, Erstattungen und Rechnungen der Anmeldungen'];
$GLOBALS['TL_LANG']['MOD']['event_setup'] = ['Einstellungen', 'Buchungseinstellungen, E-Mail-Vorlagen, Berichte, Anmeldefelder, Zahlungsanbieter und Prüfungen'];
$GLOBALS['TL_LANG']['MOD']['event_fields'] = ['Anmeldefelder', 'Die zusätzlichen Felder des Gast-Anmeldeformulars, gewählt pro Kalender, Veranstaltungsgruppe oder Veranstaltung'];
$GLOBALS['TL_LANG']['MOD']['event_payment_providers'] = ['Zahlungsanbieter', 'Die Konten für Kartenzahlungen: Stripe, PayPal, Mollie oder GOV.UK Pay'];
$GLOBALS['TL_LANG']['MOD']['event_health'] = ['Prüfung', 'Einstellungen, die wahrscheinlich Aufmerksamkeit brauchen: E-Mails ohne Versand, Veranstaltungen ohne Kurs oder Platzlimit, alte personenbezogene Daten'];
$GLOBALS['TL_LANG']['MOD']['event_retention'] = ['Alte Anmeldedaten', 'Vergangene Veranstaltungen, deren Anmeldungen noch personenbezogene Daten enthalten: prüfen und die Anmeldungen löschen oder anonymisieren'];
$GLOBALS['TL_LANG']['MOD']['event_reports'] = ['Berichte', 'Auslastung, Stornierungen, Teilnahme und Feedback je Kurs und Monat'];
$GLOBALS['TL_LANG']['MOD']['event_settings'] = ['Buchungseinstellungen: Allgemein', 'Funktionen, neue Kalender, Berichte, Texte des Buchungsformulars und Aufbewahrung der Anmeldedaten'];
$GLOBALS['TL_LANG']['MOD']['event_settings_payments'] = ['Buchungseinstellungen: Zahlungen', 'Zahlungen ein- oder ausschalten'];
$GLOBALS['TL_LANG']['MOD']['event_settings_emails'] = ['Buchungseinstellungen: E-Mail-Signaturen', 'Absender und Signatur der Veranstaltungs-E-Mails'];
$GLOBALS['TL_LANG']['MOD']['event_settings_certificates'] = ['Buchungseinstellungen: Bescheinigungen', 'Teilnahmebescheinigungen und ihr Inhalt'];
$GLOBALS['TL_LANG']['MOD']['event_settings_mailings'] = ['Buchungseinstellungen: Vor und nach Veranstaltungen', 'Trainer-Mail und Dankes-E-Mail, die die Seite selbst sendet'];
$GLOBALS['TL_LANG']['MOD']['event_venues'] = ['Veranstaltungsorte', 'Die Orte, an denen Veranstaltungen stattfinden: Name, Adresse, Anfahrt und Barrierefreiheit'];
