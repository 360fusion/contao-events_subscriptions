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
    'subscription_date'        => 'Anmeldedatum',
    'subscription_type'        => 'Typ der Anmeldung',
    'subscription_waitingList' => 'Warteliste',
    'subscription_attended'    => 'Teilgenommen',
    'payment_status'           => 'Zahlungsstatus',
    'payment_amount'           => 'Betrag',
    'payment_method'           => 'Zahlungsart',
    'purchase_order'           => 'Bestellnummer / Kostenstelle',
    'payment_reference'        => 'Zahlungsreferenz',
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

// Payments
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'] = ['free' => 'Kostenlos', 'pending' => 'Zahlung ausstehend', 'unpaid' => 'Unbezahlt (Warteliste)', 'paid' => 'Bezahlt', 'invoice' => 'Rechnung', 'refunded' => 'Erstattet', 'failed' => 'Fehlgeschlagen'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.price'] = 'Preis: %s pro Person.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.priceFreeFor'] = 'Kostenlos für E-Mail-Adressen von %s.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentMethod'] = ['Wie möchten Sie bezahlen?', ''];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.purchaseOrder'] = ['Bestellnummer oder Kostenstelle', 'Erforderlich bei Zahlung auf Rechnung.'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.purchaseOrderRequired'] = 'Bitte geben Sie eine Bestellnummer oder Kostenstelle für die Zahlung auf Rechnung an.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentMessage'] = [
    'paid' => 'Vielen Dank, Ihre Zahlung ist eingegangen und Ihr Platz ist bestätigt.',
    'failed' => 'Die Zahlung wurde nicht abgeschlossen, daher wurde die Anmeldung storniert. Sie können sich erneut anmelden.',
    'pending' => 'Ihre Zahlung ist noch nicht abgeschlossen. <a href="%s">Jetzt bezahlen</a>, um Ihren Platz zu behalten.',
    'unpaid' => 'Ihre Zahlung ist noch nicht abgeschlossen. <a href="%s">Jetzt bezahlen</a>.',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentUnavailable'] = 'Kartenzahlungen sind im Moment nicht möglich. Bitte versuchen Sie es später erneut oder wählen Sie eine andere Zahlungsart.';

// Veranstaltungen › E-Mails
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTypes'] = [
    'events_subscriptions_trainer' => 'Trainer-Mail am Vortag',
    'events_subscriptions_followUp' => 'Nach der Veranstaltung (Bescheinigung, Feedback)',
    'events_subscriptions_subscribe' => 'Bei einer Anmeldung',
    'events_subscriptions_unsubscribe' => 'Bei einer Abmeldung',
    'events_subscriptions_listUpdate' => 'Beim Nachrücken von der Warteliste',
    'events_subscriptions_reminder' => 'Erinnerung vor der Veranstaltung',
    'events_subscription_event' => 'Manuell aus der Anmeldeliste gesendet',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTagGroups'] = ['attendees' => 'Teilnehmende', 'other' => 'Sonstiges', 'booking' => 'Anmeldung', 'event' => 'Veranstaltung', 'links' => 'Links', 'payment' => 'Zahlung'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags'] = [
    'booking_link' => 'Link zur Anmeldung verwalten',
    'certificate_link' => 'Link zur Bescheinigung',
    'feedback_link' => 'Feedback-Link',
    'attendee_list' => 'Liste aller Angemeldeten',
    'attendee_count' => 'Anzahl der Anmeldungen',
    'attendee_places' => 'Anzahl gebuchter Plätze',
    'subscription_details' => 'Alle Buchungsangaben',
    'signature' => 'Signatur',
    'subscription_firstname' => 'Vorname',
    'subscription_lastname' => 'Nachname',
    'subscription_email' => 'E-Mail-Adresse',
    'subscription_numberOfParticipants' => 'Anzahl Teilnehmer',
    'event_title' => 'Titel der Veranstaltung',
    'event_startDate' => 'Startdatum',
    'event_startTime' => 'Startzeit',
    'event_endDate' => 'Enddatum',
    'event_endTime' => 'Endzeit',
    'event_location' => 'Ort',
    'event_address' => 'Adresse',
    'event_teaser' => 'Teasertext',
    'days_before_event' => 'Tage bis zur Veranstaltung',
    'calendar_title' => 'Kalender',
    'event_link' => 'Link zur Veranstaltung',
    'event_ics_link' => 'Link "Zum Kalender hinzufügen"',
    'unsubscribe_link' => 'Link zum Abmelden',
    'payment_amount' => 'Betrag',
    'payment_status' => 'Zahlungsstatus',
    'payment_method' => 'Zahlungsart',
    'purchase_order' => 'Bestellnummer / Kostenstelle',
    'payment_link' => 'Link "Jetzt bezahlen"',
];

// Veranstaltungen › Einstellungen › Prüfung: [Problem, alles in Ordnung]
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.health'] = [
    'emails' => ['%s E-Mails werden verwendet, senden aber nichts (nicht eingerichtet oder ausgeschaltet)', 'Alle verwendeten E-Mails senden etwas'],
    'noConfirmation' => ['%s Kalender nehmen Anmeldungen ohne Bestätigungs-E-Mail an', 'Jeder Kalender mit Anmeldungen sendet eine Bestätigungs-E-Mail'],
    'noGroup' => ['%s kommende Veranstaltungen sind keinem Kurs (Veranstaltungsgruppe) zugeordnet', 'Alle kommenden Veranstaltungen sind einem Kurs zugeordnet'],
    'noLimit' => ['%s kommende Veranstaltungen haben kein Platzlimit', 'Alle kommenden Veranstaltungen haben ein Platzlimit'],
    'noPayment' => ['%s kommende Veranstaltungen haben einen Preis, aber keine anbietbare Zahlungsart (Karte braucht einen Zahlungsanbieter)', 'Alle kostenpflichtigen Veranstaltungen können Zahlungen annehmen'],
    'emptyGroups' => ['%s veröffentlichte Kurse haben keine kommenden Termine', 'Alle veröffentlichten Kurse haben kommende Termine'],
    'retentionOff' => ['Das Entfernen alter personenbezogener Daten ist ausgeschaltet: %s Anmeldungen gehören zu Veranstaltungen, die vor mehr als %s Monaten endeten', 'Keine Anmeldungen zu Veranstaltungen älter als %2$s Monate'],
    'retentionDue' => ['%s Anmeldungen zu Veranstaltungen älter als %s Monate sind fällig und verlieren beim nächsten täglichen Lauf ihre personenbezogenen Daten', 'Keine Anmeldungen zu Veranstaltungen älter als %2$s Monate'],
    'more' => '… und %s weitere',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.formIntro'] = 'Bitte melden Sie sich mit dem folgenden Formular für diese Veranstaltung an.';

// The booker's own pages (booking, certificate, feedback, check-in)
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.public'] = [
    'bookingTitle' => 'Ihre Anmeldung',
    'notFound' => 'Dieser Link ist nicht mehr gültig. Die Anmeldung wurde möglicherweise storniert.',
    'event' => 'Veranstaltung',
    'date' => 'Datum',
    'place' => 'Ort',
    'status' => 'Status',
    'name' => 'Name',
    'statusBooked' => 'Angemeldet',
    'statusWaiting' => 'Auf der Warteliste',
    'checkinTitle' => 'Code bei der Ankunft zeigen',
    'checkinHelp' => 'Er wird am Eingang gescannt, damit Ihre Teilnahme vermerkt wird.',
    'attendedNotice' => 'Ihre Teilnahme ist vermerkt.',
    'certificateOpen' => 'Bescheinigung öffnen',
    'edit' => 'Angaben ändern',
    'emailLocked' => 'Für eine andere E-Mail-Adresse stornieren Sie diese Anmeldung und melden sich neu an.',
    'save' => 'Änderungen speichern',
    'saved' => 'Ihre Angaben wurden gespeichert.',
    'required' => 'Bitte „%s“ ausfüllen.',
    'invalid' => '„%s“ ist ungültig.',
    'cancel' => 'Meine Anmeldung stornieren',
    'cancelConfirm' => 'Diese Anmeldung stornieren?',
    'cancelled' => 'Ihre Anmeldung für „%s“ wurde storniert.',
    'cancelClosed' => 'Online-Stornierung ist für diese Veranstaltung nicht mehr möglich. Bitte wenden Sie sich an das Team.',
    'cancelPaid' => 'Sie haben diese Anmeldung bezahlt. Für eine Erstattung wenden Sie sich nach der Stornierung bitte an das Team.',
    'certificateTitle' => 'Teilnahmebescheinigung',
    'certificateCertify' => 'Hiermit wird bescheinigt, dass',
    'certificateAttended' => 'teilgenommen hat an',
    'certificateHours' => '%s Stunden',
    'certificateIssued' => 'Ausgestellt am %s',
    'certificatePrint' => 'Drucken oder als PDF speichern',
    'certificateNone' => 'Für diese Anmeldung gibt es keine Bescheinigung. Sie ist verfügbar, sobald Ihre Teilnahme vermerkt ist.',
    'feedbackTitle' => 'Wie war es?',
    'feedbackThanks' => 'Vielen Dank für Ihr Feedback.',
    'feedbackNotYet' => 'Feedback ist möglich, sobald die Veranstaltung begonnen hat.',
    'feedbackRating' => 'Ihre Bewertung',
    'feedbackRatingMissing' => 'Bitte wählen Sie eine Bewertung.',
    'feedbackComment' => 'Möchten Sie uns noch etwas mitteilen? (optional)',
    'feedbackSend' => 'Feedback senden',
    'rating1' => '1 - Schlecht',
    'rating2' => '2 - Mäßig',
    'rating3' => '3 - Gut',
    'rating4' => '4 - Sehr gut',
    'rating5' => '5 - Ausgezeichnet',
    'checkinPage' => 'Check-in',
    'checkinStaffOnly' => 'Nur für Mitarbeitende. Im Backend anmelden und den Code erneut scannen.',
    'signIn' => 'Anmelden',
    'checkinButton' => 'Einchecken',
    'checkinDone' => '%s ist eingecheckt.',
    'checkinAlready' => '%s war bereits eingecheckt.',
    'checkinWaiting' => 'Diese Anmeldung steht auf der Warteliste.',
    'checkinWrongDay' => 'Hinweis: Diese Anmeldung gilt für %s, nicht für heute.',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.noAttendees'] = 'Es gibt keine Anmeldungen.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.sameCourseError'] = 'Sie sind bereits für einen anderen Termin dieses Kurses angemeldet: %s (%s). Bitte stornieren Sie diese Anmeldung zuerst über den Link in Ihrer Bestätigungs-E-Mail.';

// Veranstaltungsorte
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.venueDirections'] = 'Anfahrt:';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.venueAccessibility'] = 'Barrierefreiheit:';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.venueMap'] = 'Karte ansehen';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags'] += ['event_venue_directions' => 'Anfahrt zum Ort', 'event_venue_accessibility' => 'Barrierefreiheit des Ortes', 'event_venue_map' => 'Kartenlink des Ortes'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags'] += ['event_trainer_name' => 'Name des Trainers'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags'] += ['payment_invoice_text' => 'Hinweise zur Rechnung'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentUnavailableEvent'] = 'Die Anmeldung ist im Moment nicht möglich. Bitte versuchen Sie es später erneut oder wenden Sie sich an das Team.';
