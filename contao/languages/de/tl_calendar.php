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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_enable']       = [
    'Anmeldung aktivieren',
    'Besuchern erlauben sich für Events dieses Kalenders anzumelden.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders']    = [
    'Erinnerungen aktivieren',
    'Aktiviere Erinnerungen per E-Mail für Events dieses Kalenders.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_time']         = [
    'Sendezeit',
    'Bitte geben Sie die ungefähre Zeit ein, wann Erinnerungen gesendet werden sollen.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_days']         = [
    'Tage vor dem Event',
    'Bitte geben Sie in einer Komma getrennten Liste an, wieviele Tage vor dem Event Erinnerungsmails verschickt werden sollen.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_notification'] = [
    'Benachrichtigung für Erinnerungen',
    'Bitte wählen Sie die Benachrichtigung, welche die Erinnerung versenden soll.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_skipWaitingListReminders'] = [
    'Erinnerungen für Anmeldungen auf der Wartelise überspringen',
    'Sende keine Erinnerungen für Besucher, welche zur Zeit auf der Warteliste stehen.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeLinkPage'] = [
    'Link zur Seite mit der Abmeldebestätigung',
    'Hier können Sie die Seite wählen, welche angezeigt wird wenn der Besucher sich per eindeutigem Link aus einer erhaltenen E-Mail abmeldet.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_subscribeNotification'] = [
    'Benachrichtigung über Anmeldung',
    'Hier können Sie eine benutzerdefinierte Benachrichtigung für die Anmeldung wählen, die anstelle der Standard-Benachrichtigungen dieses Typs verwendet wird.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeNotification'] = [
    'Benachrichtigung über Abmeldung',
    'Hier können Sie eine benutzerdefinierte Benachrichtigung für die Abmeldung wählen, die anstelle der Standard-Benachrichtigungen dieses Typs verwendet wird.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_listUpdateNotification'] = [
    'Benachrichtigung über Veränderung der Warteliste',
    'Hier können Sie eine benutzerdefinierte Benachrichtigung über Anderungen in der Warteliste wählen, die anstelle der Standard-Benachrichtigungen dieses Typs verwendet wird.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Anmeldung Einstellungen';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview'] = ['Anmeldungen Übersicht', 'Zeige eine Übersicht der Anmeldungen des Kalenders ID %s'];

/**
 * Export
 */
$GLOBALS['TL_LANG']['tl_calendar']['export.headline'] = 'Anmeldungen exportieren';
$GLOBALS['TL_LANG']['tl_calendar']['export.explanation'] = 'Sie sind dabei, die Anmeldedaten dieses Kalenders zu exportieren:';
$GLOBALS['TL_LANG']['tl_calendar']['export.explanationFilters'] = 'Mit den nachfolgenden Feldern kann der export auf einen beliebigen Zeitraum eingeschränkt werden. Der Filter wird auf das Datum der Anmeldung angewendet.';
$GLOBALS['TL_LANG']['tl_calendar']['export.startDate'] = ['Start-Datum', 'Bitte Start-Datum der Anmeldungen eintragen'];
$GLOBALS['TL_LANG']['tl_calendar']['export.endDate'] = ['End-Datum', 'Bitte End-Datum der Anmeldungen eintragen'];
$GLOBALS['TL_LANG']['tl_calendar']['export.excelFormatHint'] = 'Um einen Export im Excel-Format zu generieren, bitte das <strong>phpoffice/phpspreadsheet</strong> Paket installieren. Alternativ kann auch das veraltete <strong>phpoffice/phpexcel</strong> Paket installiert werden.';
$GLOBALS['TL_LANG']['tl_calendar']['export.csv'] = 'Export als CSV';
$GLOBALS['TL_LANG']['tl_calendar']['export.excel'] = 'Export als Excel';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.headline'] = 'Anmeldeübersicht für Kalender "%s"';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.empty'] = 'Derzeit gibt es keine Anmeldungen.';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.waitingList'] = 'Warteliste';

/**
 * Global operations
 */
$GLOBALS['TL_LANG']['tl_calendar']['registration_fields'] = ['Anmeldefelder', 'Die zusätzlichen Felder des Gast-Anmeldeformulars verwalten'];

$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_summary'] = '%s Kalender · %s aktuelle Events · %s Anmeldungen zu aktuellen Events';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_summary.link'] = 'Kommende Events und Anmeldungen anzeigen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.headline'] = 'Kommende Events und Anmeldungen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming'] = ['Kommende Events und Anmeldungen', 'Kommende Events und Anmeldungen'];

/**
 * Upcoming events filters and calendar groups
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.search'] = 'Event';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.calendar'] = 'Kalender';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.group'] = 'Kalendergruppe';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.from'] = 'Von';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.to'] = 'Bis';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.places'] = 'Plätze';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.placesAll'] = 'Alle Events';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.placesAvailable'] = 'Plätze frei';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.placesFull'] = 'Ausgebucht';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.apply'] = 'Filtern';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.reset'] = 'Zurücksetzen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.summary'] = '%s Events · %s Plätze belegt';
$GLOBALS['TL_LANG']['tl_calendar']['calendarGroup'] = ['Kalendergruppe', 'Die Gruppe, zu der dieser Kalender gehört.'];
$GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'] = ['Kalendergruppen', 'Die Kalendergruppen verwalten'];

$GLOBALS['TL_LANG']['tl_calendar']['showEvents'] = 'Die Events des Kalenders ID %s anzeigen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.dynamicSearch'] = 'Dynamische Suche';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.dynamicSearchPlaceholder'] = 'Eventtitel durchsuchen…';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.dynamicSearchHelp'] = 'Beliebige Wörter aus einem Eventtitel in beliebiger Reihenfolge eingeben. Die Liste aktualisiert sich während der Eingabe.';
$GLOBALS['TL_LANG']['tl_calendar']['alias'] = ['Alias', 'Wird in den Links der Veranstaltungsnavigation verwendet (?calendar=alias). Wird aus dem Titel erzeugt, wenn leer.'];
$GLOBALS['TL_LANG']['tl_calendar']['event_groups'] = ['Veranstaltungsgruppen', 'Die Veranstaltungsgruppen von Kalender ID %s verwalten'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.eventGroup'] = 'Veranstaltungsgruppe';
$GLOBALS['TL_LANG']['tl_calendar']['payments'] = ['Zahlungen', 'Alle Zahlungen anzeigen'];
$GLOBALS['TL_LANG']['tl_calendar']['payment_providers'] = ['Zahlungsanbieter', 'Die Zahlungsanbieter einrichten'];
$GLOBALS['TL_LANG']['tl_calendar']['emails'] = ['E-Mails', 'Die E-Mails zu den Anmeldungen bearbeiten'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.when'] = 'Veranstaltungen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.whenAll'] = 'Alle Veranstaltungen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.whenUpcoming'] = 'Kommende';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.whenPast'] = 'Vergangene';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_people'] = ['Teilnehmer', 'Alle Anmeldungen der Veranstaltungen von Kalender ID %s'];
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.headline'] = 'Teilnehmer des Kalenders "%s"';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.summary'] = '%s Anmeldungen · %s Plätze';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.name'] = 'Name';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.event'] = 'Veranstaltung';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.bookedOn'] = 'Angemeldet am';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.status'] = 'Status';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.attended'] = 'Teilgenommen';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.payment'] = 'Zahlung';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.booked'] = 'Angemeldet';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.waitingList'] = 'Warteliste';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.participants'] = '%s Teilnehmer';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.edit'] = 'Anmeldung bearbeiten';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.eventSubscriptions'] = 'Alle Anmeldungen dieser Veranstaltung';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.searchPlaceholder'] = 'Namen, E-Mail-Adressen und Veranstaltungen suchen…';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.searchHelp'] = 'Beliebige Wörter aus Name, E-Mail-Adresse oder Veranstaltungstitel eingeben, in beliebiger Reihenfolge. Die Liste wird beim Tippen aktualisiert.';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers'] = ['Teilnehmer', 'Alle Anmeldungen aller Kalender'];
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.headlineAll'] = 'Alle Teilnehmer';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_peopleAll'] = ['Alle Teilnehmer', 'Alle Anmeldungen aller Kalender'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_export'] = ['Anmeldungen exportieren', 'Anmeldungen des aktuellen Kalenders exportieren'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.title'] = 'Unterschriftenliste';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.print'] = 'Drucken';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.showAnswers'] = 'Angaben anzeigen';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.hideAnswers'] = 'Angaben ausblenden';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.summary'] = '%s Anmeldungen, %s Plätze';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.name'] = 'Name';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.email'] = 'E-Mail-Adresse';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.places'] = 'Plätze';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.answers'] = 'Angaben';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.signature'] = 'Unterschrift';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.waiting'] = 'Warteliste';
