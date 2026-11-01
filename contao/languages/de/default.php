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
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribe']               = 'Anmelden';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeWaitingList']    = 'Auf die Warteliste setzen';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribe']             = 'Abmelden';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeWaitingList']  = 'Von der Warteliste abmelden';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation']   = 'Sie haben sich für das Event angemeldet.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'] = 'Sie haben sich vom Event abgemeldet.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeNotAllowed']     = 'Anmeldung zu diesem Event ist nicht mehr möglich.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeNotAllowed']   = 'Abmeldung von diesem Event ist nicht mehr möglich.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']           = 'Warteliste';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.enableReminders']         = 'Sende mir eine Erinnerung für dieses Event';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.canSubscribeUntil']       = 'Anmeldung möglich bis: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.canUnsubscribeUntil']     = 'Abmeldung möglich bis: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants']    = 'Anzahl der Teilnehmer';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsLimit'] = 'Sie können bis zu %d Teilnehmer für diese Veranstaltung anmelden.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailDomainError'] = 'Nur E-Mail-Adressen aus diesen Domains können sich anmelden: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailUniqueError'] = 'Diese E-Mail-Adresse ist bereits für dieses Event angemeldet.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceeded'] = 'Leider können Sie sich nicht anmelden, da die maximale Teilnehmerzahl für dieses Event %d beträgt.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceededWaitingList'] = 'Leider können Sie sich nicht anmelden, da die maximale Teilnehmerzahl für dieses Event %d beträgt mit einer zusätzlichen Warteliste von %d Plätzen.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsSubscribeToWaitingList'] = 'Leider können Sie sich nicht anmelden, da die maximale Teilnehmerzahl für dieses Event %d beträgt. Für eine Anmeldung auf die Warteliste senden Sie das Formular bitte erneut.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname']     = 'Vorname';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname']      = 'Nachname';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email']         = 'E-Mail-Adresse';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'] = 'Mitglied ID %s ist für dieses Event bereits angemeldet!';

/**
 * Export
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'] = [
    'event_id'                 => 'Event ID',
    'event_title'              => 'Event Titel',
    'event_group'              => 'Veranstaltungsgruppe',
    'event_start'              => 'Event Beginn',
    'event_end'                => 'Event Ende',
    'subscription_type'        => 'Typ der Anmeldung',
    'subscription_waitingList' => 'Warteliste',
    'subscription_attended'    => 'Teilgenommen',
    'subscription_firstname'   => 'Vorname',
    'subscription_lastname'    => 'Nachname',
    'subscription_email'       => 'E-Mail-Adresse',
    'subscription_numberOfParticipants' => 'Anzahl der Teilnehmer',
];

$GLOBALS['TL_LANG']['MSC']['events_subscriptions.memberExportHeaderFields'] = [
    'member_id'       => 'Mitglied ID',
    'member_username' => 'Mitglied Benutzername',
];

// Registration fields
$GLOBALS['TL_LANG']['MSC']['subscriptionFieldDeleteConfirm'] = 'Dieses Feld löschen? Bereits gegebene Antworten werden entfernt, sobald eine Anmeldung das nächste Mal im Backend gespeichert wird. Um sie zu behalten, deaktivieren Sie das Feld stattdessen.';
// Booking status
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscriptionClosed'] = 'Die Anmeldung für dieses Event ist geschlossen.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.eventFull'] = 'Dieses Event ist ausgebucht.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.eventFullWaitingList'] = 'Dieses Event ist ausgebucht. Sie können sich noch auf die Warteliste setzen.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.placesLeft'] = '%s von %s Plätzen frei';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.fullyBooked'] = 'Ausgebucht';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.fullyBookedWaitingList'] = 'Ausgebucht – Warteliste offen';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.bookingCloses'] = 'Anmeldeschluss: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.bookingClosed'] = 'Anmeldung geschlossen';

$GLOBALS['TL_LANG']['MSC']['eventNavigation.label'] = 'Veranstaltungen';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.all'] = 'Alle Veranstaltungen';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.calendar'] = 'Kategorie';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.group'] = 'Veranstaltungsgruppe';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.month'] = 'Monat';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.anyMonth'] = 'Alle Monate';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.filter'] = 'Filtern';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.reset'] = 'Zurücksetzen';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.addToCalendar'] = 'Zum Kalender hinzufügen';
