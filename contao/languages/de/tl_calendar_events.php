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
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_override']           = [
    'Anmeldeeinstellungen überschreiben',
    'Überschreiben Sie die Anmeldeeinstellungen des Kalenders.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_types']              = [
    'Erlaubte Anmelde-Typen',
    'Hier können Sie die erlaubten Anmelde-Typen auswählen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum']            = [
    'Maximale Anzahl der Anmeldungen',
    'Hier können Sie die maximale Anzahl der Anmeldungen pro Event angeben. 0 steht für unbegrenzte Anmeldungen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime']   = [
    'Anmeldeschluss',
    'Hier können Sie den Zeitversatz vor Beginn des Events festlegen bis wann eine Anmeldung erfolgen kann. Lassen Sie das Feld leer, wenn Sie keinen Anmeldeschluss benötigen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeEndTime'] = [
    'Abmeldefrist',
    'Hier können Sie den Zeitversatz vor Beginn des Events festlegen ab wann eine Abmeldung nicht mehr möglich ist. Lassen Sie das Feld leer, wenn Sie dies nicht benötigen.'
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_numberOfParticipants'] = ['Enable number of participants', 'Erlaube die Angabe einer Anzahl an Teilnehmern bei der Anmeldung'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_numberOfParticipantsLimit'] = ['Teilnehmerbegrenzung', 'Hier können Sie die Anzahl der Teilnehmer pro Anmeldung begrenzen. Geben Sie 0 ein, um unbegrenzte Teilnehmer zu erlauben.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingList']        = [
    'Warteliste aktivieren',
    'Erlauben Sie die Anmeldung auf die Warteliste.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingListLimit']   = [
    'Warteliste begrenzen',
    'Bitte geben Sie das Limit der Warteliste an. 0 für keine Begrenzung.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroupsLimit']   = [
    'Beschränkung auf Mitgliedergruppen',
    'Beschränken Sie die Anmeldung auf bestimmte Mitgliedergruppen. Dies funktioniert nur, wenn Sie den Anmelde-Typ für Mitglieder aktiviert haben!',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroups']   = [
    'Mitgliedergruppen',
    'Bitte wählen Sie eine oder mehrere Mitgliedergruppen, welche sich anmelden dürfen.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_lastNotificationSent'] = ['Letzte Benachrichtigung gesendet'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_legend'] = ' Anmeldung Einstellungen';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'] = [
    'Anmeldungen',
    'Zeigt die Anmeldungen zu Event ID %s.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'] = ['Event-Benachrichtigungen senden', 'Benachrichtigungen über Event ID %s senden'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'] = [
    'seconds' => 'Sekunde(n)',
    'minutes' => 'Minute(n)',
    'hours'   => 'Stunde(n)',
    'days'    => 'Tag(e)',
    'weeks'   => 'Woche(n)',
    'months'  => 'Monat(e)',
    'years'   => 'Jahr(e)',
];

/**
 * Registration fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields'] = ['Anmeldefelder', 'Wählen Sie die zusätzlichen Felder des Gast-Anmeldeformulars in der gewünschten Reihenfolge. Die Felder werden unter Events → Anmeldefelder verwaltet.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_field'] = ['Feld'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatory'] = ['Pflichtfeld'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef'][''] = 'Standard des Feldes';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef']['1'] = 'Pflichtfeld';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef']['0'] = 'Optional';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_disabled'] = '%s (deaktiviert)';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_emailDomains'] = ['Erlaubte E-Mail-Domains', 'Nur Gäste mit einer E-Mail-Adresse aus einer dieser Domains können sich anmelden. Eine Domain pro Zeile: "example.com" erlaubt auch Subdomains, "@example.com" nur genau diese Domain. Leer lassen, um alle E-Mail-Adressen zu erlauben.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_uniqueEmails'] = ['Eine Anmeldung pro E-Mail-Adresse', 'Eine Gast-E-Mail-Adresse kann sich nur einmal für ein Event anmelden.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_disableReminders'] = ['Erinnerungen immer senden', 'Die Checkbox zum Abbestellen der Erinnerungen im Anmeldeformular ausblenden, sodass alle Angemeldeten die Erinnerungen erhalten. Gilt nur, wenn Erinnerungen im Kalender aktiviert sind.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customEmailDomains'] = ['Eigene E-Mail-Domains', 'Für dieses Event eine eigene Liste erlaubter E-Mail-Domains statt der Liste des Kalenders verwenden. Eine leere Liste erlaubt alle E-Mail-Adressen.'];
// Notifications per event
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customNotifications'] = ['Eigene Benachrichtigungen', 'Für dieses Event andere Benachrichtigungen als die des Kalenders verwenden, z. B. um ein Infopaket zu diesem Event zu senden. Leere Felder verwenden die Benachrichtigung des Kalenders.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeNotification'] = ['Anmelde-Benachrichtigung', 'Die Benachrichtigung bei der Anmeldung. Leer lassen, um die des Kalenders zu verwenden.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeNotification'] = ['Abmelde-Benachrichtigung', 'Die Benachrichtigung bei der Abmeldung. Leer lassen, um die des Kalenders zu verwenden.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_listUpdateNotification'] = ['Nachrück-Benachrichtigung', 'Die Benachrichtigung, wenn jemand von der Warteliste nachrückt. Leer lassen, um die des Kalenders zu verwenden.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_notification'] = ['Erinnerungs-Benachrichtigung', 'Die Erinnerungs-Benachrichtigung, sofern der Kalender Erinnerungen sendet. Leer lassen, um die des Kalenders zu verwenden.'];

$GLOBALS['TL_LANG']['tl_calendar_events']['eventGroup'] = ['Veranstaltungsgruppe', 'Die Veranstaltungsgruppe (zum Beispiel der Kurs) in diesem Kalender. Die Gruppen werden über das Ordnersymbol in der Kalenderliste verwaltet.'];
