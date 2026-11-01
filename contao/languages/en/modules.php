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
$GLOBALS['TL_LANG']['FMD']['event_list_subscribe']   = [
    'Event list subscribe',
    'Adds a list of subscribable events to the page.',
];
$GLOBALS['TL_LANG']['FMD']['event_reader_subscribe'] = [
    'Event reader subscribe',
    'Adds a reader of subscribable event to the page.',
];
$GLOBALS['TL_LANG']['FMD']['event_subscribe']        = [
    'Subscribe event form',
    'Adds a subscribe event form to the page.',
];
$GLOBALS['TL_LANG']['FMD']['event_subscriptions']    = [
    'User event subscriptions',
    'Adds a list of events that the current user is subscribed to.',
];
$GLOBALS['TL_LANG']['FMD']['event_navigation']     = [
    'Event navigation',
    'Lists the calendars and their event groups as navigation or filter, linking to an event list with "Filter by URL".',
];

/**
 * Back end menu: the "Events" group
 */
$GLOBALS['TL_LANG']['MOD']['events_subscriptions'] = 'Events';
$GLOBALS['TL_LANG']['MOD']['calendar'] = ['Calendars', 'Manage the calendars, their events and bookings'];
$GLOBALS['TL_LANG']['MOD']['event_upcoming'] = ['Upcoming events', 'The upcoming events of all calendars and their bookings'];
$GLOBALS['TL_LANG']['MOD']['event_subscribers'] = ['Subscribers', 'Everyone booked on the events of all calendars'];
$GLOBALS['TL_LANG']['MOD']['event_emails'] = ['E-mails', 'The e-mails sent for bookings'];
$GLOBALS['TL_LANG']['MOD']['event_payments'] = ['Payments', 'Payments, refunds and invoices of bookings'];
$GLOBALS['TL_LANG']['MOD']['event_setup'] = ['Setup', 'Registration fields, payment providers and other booking settings'];
$GLOBALS['TL_LANG']['MOD']['event_fields'] = ['Registration fields', 'The extra fields guests fill in when booking, chosen per calendar, event group or event'];
$GLOBALS['TL_LANG']['MOD']['event_payment_providers'] = ['Payment providers', 'The accounts for card payments: Stripe, PayPal, Mollie or GOV.UK Pay'];
$GLOBALS['TL_LANG']['MOD']['event_settings'] = ['Booking settings', 'The booking settings new calendars start with, and how long booking details are kept'];
$GLOBALS['TL_LANG']['MOD']['event_health'] = ['Health check', 'Settings that probably need attention: e-mails that send nothing, events without a course or a place limit, old personal data'];
$GLOBALS['TL_LANG']['MOD']['event_retention'] = ['Old booking data', 'Past events whose bookings still have personal data: review them and delete or anonymise the bookings'];
$GLOBALS['TL_LANG']['MOD']['event_reports'] = ['Reports', 'Fill rate, cancellations, attendance and feedback for each course and month'];
