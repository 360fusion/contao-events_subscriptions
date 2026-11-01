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
    'Override subscription settings',
    'Override the subscription settings set in the calendar settings.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_types']              = [
    'Allowed subscription types',
    'Here you can choose the allowed subscription types.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum']            = [
    'Maximum number of subscriptions',
    'Here you can set the maximum number of subscriptions per event. Leave 0 to allow unlimited subscriptions.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime']   = [
    'Subscribe end time',
    'Here you can set the time offset before start of the event when the subscription closes. Leave empty to not limit the time to subscribe.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeEndTime'] = [
    'Unsubscribe end time',
    'Here you can set the time offset before start of the event when the unsubscription closes. Leave empty to not limit the time to unsubscribe.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_numberOfParticipants'] = ['Enable number of participants', 'Allow subscribers to enter the number of participants.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_numberOfParticipantsLimit'] = ['Number of participants limit', 'Here you can limit the number of participants per subscription. Leave zero to allow unlimited participants.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingList']        = [
    'Enable waiting list',
    'Allow the users to subscribe to the waiting list.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingListLimit']   = [
    'Waiting list limit',
    'Please enter the waiting list limit. Enter 0 to allow unlimited subscriptions.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroupsLimit']   = [
    'Member groups restriction',
    'Restrict the subscription to certain member groups. This works only if you have enabled the Member subscription type!',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_memberGroups']   = [
    'Member groups',
    'Please choose one or more member groups that are allowed to subscribe.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_lastNotificationSent'] = ['Last notification sent'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_legend'] = 'Subscription settings';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'] = [
    'Subscriptions',
    'Show the subscriptions of event ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'] = ['Send event notifications', 'Send a notifications of event ID %s'];

// Backend breadcrumb
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions_notification'] = &$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'] = [
    'seconds' => 'second(s)',
    'minutes' => 'minute(s)',
    'hours'   => 'hour(s)',
    'days'    => 'day(s)',
    'weeks'   => 'week(s)',
    'months'  => 'month(s)',
    'years'   => 'year(s)',
];

/**
 * Registration fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields'] = ['Registration fields', 'Choose the additional fields of the guest registration form, in order. Fields are managed under Events → Registration fields.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_field'] = ['Field'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatory'] = ['Required'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef'][''] = 'Field default';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef']['1'] = 'Required';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef']['0'] = 'Optional';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_disabled'] = '%s (disabled)';
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_emailDomains'] = ['Allowed e-mail domains', 'Only guests with an e-mail address from one of these domains can subscribe. Enter one domain per line: "example.com" also allows its subdomains, "@example.com" allows only this domain. Leave empty to allow all e-mail addresses.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_uniqueEmails'] = ['One subscription per e-mail address', 'Allow a guest e-mail address to subscribe to an event only once.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_disableReminders'] = ['Always send reminders', 'Hide the reminders opt-out checkbox on the subscription form, so every subscriber receives the reminders. Only applies if reminders are enabled in the calendar.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customEmailDomains'] = ['Custom e-mail domains', 'Use a custom list of allowed e-mail domains for this event instead of the calendar\'s list. An empty list allows all e-mail addresses.'];
// Notifications per event
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customNotifications'] = ['Custom notifications', 'Use other notifications for this event than the calendar\'s, e.g. to send an information pack for this event. Fields left empty use the calendar\'s notification.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeNotification'] = ['Subscribe notification', 'The notification sent on subscription. Leave empty to use the calendar\'s.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeNotification'] = ['Unsubscribe notification', 'The notification sent on unsubscription. Leave empty to use the calendar\'s.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_listUpdateNotification'] = ['Waiting list promotion notification', 'The notification sent when a subscriber moves up from the waiting list. Leave empty to use the calendar\'s.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_notification'] = ['Reminder notification', 'The reminder notification, if the calendar sends reminders. Leave empty to use the calendar\'s.'];

$GLOBALS['TL_LANG']['tl_calendar_events']['eventGroup'] = ['Event group', 'The event group (for example the course) within this calendar. Manage the groups with the folder icon in the calendar list.'];

// Payments
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customPayment'] = ['Custom price and payment', 'Use another price or other payment settings for this event than the calendar\'s.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_price'] = ['Price per person', 'The price in the provider\'s currency, e.g. 12.50. Leave empty or 0 for free.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethods'] = ['Payment methods', 'How people can pay. Card payments need a payment provider.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethodsRef'] = ['card' => 'Card (online)', 'invoice' => 'Invoice / purchase order / cost centre'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentProvider'] = ['Payment provider', 'The provider for card payments, set up in Events › Settings › Payment providers.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_freeEmailDomains'] = ['Free for these e-mail domains', 'Guests with an e-mail address of these domains book free, e.g. nhs.net (also allows subdomains) or @nhs.net (exact domain only).'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_trainerEmail'] = ['Trainer e-mail address', 'Gets the trainer pack the day before: the list of everyone booked. Use it for a trainer who is not in the Trainers list; a trainer chosen above comes first. On an event, leave empty to use the trainer of its course.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptionsRegister'] = ['Sign-in sheet', 'Print the list of everyone booked with a column to sign'];
$GLOBALS['TL_LANG']['tl_calendar_events']['venue'] = ['Venue', 'Choose a place from Venues and locations. The location and address are filled in from it when you save, and shown on the website. Leave empty to type them in yourself.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_trainer'] = ['Trainer', 'Choose a trainer from Trainers. They get the trainer pack the day before. A trainer chosen here is used instead of the e-mail address typed below. On an event, leave empty to use the trainer of its course.'];
