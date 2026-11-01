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
    'Enable the subscriptions',
    'Allow the users to subscribe to the events of this calendar.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_reminders']    = [
    'Enable reminders',
    'Enable the e-mail reminders for events in the calendar.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_time']         = [
    'Sending time',
    'Please enter the approximate time when reminders should be send.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_days']         = [
    'Days before event',
    'Please enter the comma separated days before event when the reminders should be send.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_notification'] = [
    'Reminder notification',
    'Please choose the notification that will be used to sent the reminder.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_skipWaitingListReminders'] = [
    'Skip reminders for waiting list subscribers',
    'Do not send reminders for subscribers that are currently on the waiting list.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeLinkPage'] = [
    'Unsubscribe link confirmation page',
    'Here you can choose the page that will be displayed after the user unsubscribes via unique link he receives in e-mail.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_subscribeNotification'] = [
    'Subscribe notification',
    'Here you can choose a custom subscribe notification that will be used instead of all notifications of this type.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_unsubscribeNotification'] = [
    'Unsubscribe notification',
    'Here you can choose a custom unsubscribe notification that will be used instead of all notifications of this type.',
];
$GLOBALS['TL_LANG']['tl_calendar']['subscription_listUpdateNotification'] = [
    'Waiting list promotion notification',
    'Here you can choose a custom waiting list promotion notification that will be used instead of all notifications of this type.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscription_legend'] = 'Subscription settings';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview'] = ['Subscriptions overview', 'View an overview of subscriptions of calendar ID %s'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_export'] = ['Subscriptions export', 'Export subscriptions of the current calendar'];

/**
 * Export
 */
$GLOBALS['TL_LANG']['tl_calendar']['export.headline'] = 'Export subscriptions';
$GLOBALS['TL_LANG']['tl_calendar']['export.explanation'] = 'You are about to export the subscriptions data for the calendar:';
$GLOBALS['TL_LANG']['tl_calendar']['export.explanationFilters'] = 'Use the fields below to limit the exported subscriptions by date bounds. The filter will affect the date of subscription.';
$GLOBALS['TL_LANG']['tl_calendar']['export.startDate'] = ['Start date', 'Here you can enter the subscriptions start date.'];
$GLOBALS['TL_LANG']['tl_calendar']['export.endDate'] = ['End date', 'Here you can enter the subscriptionsm end date.'];
$GLOBALS['TL_LANG']['tl_calendar']['export.excelFormatHint'] = 'To enable export in Excel format please install the <strong>phpoffice/phpspreadsheet</strong> package. Alternatively you can install the deprecated <strong>phpoffice/phpexcel</strong> package.';
$GLOBALS['TL_LANG']['tl_calendar']['export.csv'] = 'Export as CSV';
$GLOBALS['TL_LANG']['tl_calendar']['export.excel'] = 'Export as Excel';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.headline'] = 'Subscriptions overview for calendar "%s"';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.empty'] = 'Currently there are no subscriptions.';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview.waitingList'] = 'Waiting list';

/**
 * Global operations
 */
$GLOBALS['TL_LANG']['tl_calendar']['registration_fields'] = ['Registration fields', 'Manage the additional fields of the guest registration form'];

$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_summary'] = '%s calendars · %s live events · %s subscriptions to live events';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_summary.link'] = 'View upcoming events and subscriptions';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.headline'] = 'Upcoming events and subscriptions';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming'] = ['Upcoming events and subscriptions', 'Upcoming events and subscriptions'];

/**
 * Upcoming events filters and calendar groups
 */
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.search'] = 'Event';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.calendar'] = 'Calendar';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.group'] = 'Calendar group';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.from'] = 'From';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.to'] = 'To';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.places'] = 'Places';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.placesAll'] = 'All events';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.placesAvailable'] = 'Places left';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.placesFull'] = 'Full';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.apply'] = 'Filter';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.reset'] = 'Reset';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.summary'] = '%s events · %s places taken';
$GLOBALS['TL_LANG']['tl_calendar']['calendarGroup'] = ['Calendar group', 'The group this calendar belongs to.'];
$GLOBALS['TL_LANG']['tl_calendar']['calendar_groups'] = ['Calendar groups', 'Manage the calendar groups'];

$GLOBALS['TL_LANG']['tl_calendar']['showEvents'] = 'Show the events of calendar ID %s';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.dynamicSearch'] = 'Dynamic search';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.dynamicSearchPlaceholder'] = 'Search event titles…';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.dynamicSearchHelp'] = 'Type any words from an event title, in any order. The list updates as you type.';
$GLOBALS['TL_LANG']['tl_calendar']['alias'] = ['Alias', 'Used in the event navigation links (?calendar=alias). Generated from the title if empty.'];
$GLOBALS['TL_LANG']['tl_calendar']['event_groups'] = ['Event groups', 'Manage the event groups of calendar ID %s'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.eventGroup'] = 'Event group';
$GLOBALS['TL_LANG']['tl_calendar']['payments'] = ['Payments', 'Show all payments'];
$GLOBALS['TL_LANG']['tl_calendar']['payment_providers'] = ['Payment providers', 'Set up the payment providers'];
$GLOBALS['TL_LANG']['tl_calendar']['emails'] = ['E-mails', 'Edit the e-mails sent for bookings'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.when'] = 'Events';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.whenAll'] = 'All events';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.whenUpcoming'] = 'Upcoming';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_upcoming.whenPast'] = 'Past';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_people'] = ['Subscribers', 'Everyone booked on the events of calendar ID %s'];
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.headline'] = 'Subscribers of calendar "%s"';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.summary'] = '%s bookings · %s places';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.name'] = 'Name';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.event'] = 'Event';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.bookedOn'] = 'Booked on';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.status'] = 'Status';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.attended'] = 'Attended';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.payment'] = 'Payment';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.booked'] = 'Booked';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.waitingList'] = 'Waiting list';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.participants'] = '%s participants';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.edit'] = 'Edit the booking';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.eventSubscriptions'] = 'All bookings of this event';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.searchPlaceholder'] = 'Search names, e-mail addresses and events…';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.searchHelp'] = 'Type any words from a name, e-mail address or event title, in any order. The list updates as you type.';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers'] = ['Subscribers', 'Everyone booked on the events of all calendars'];
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.headlineAll'] = 'All subscribers';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_peopleAll'] = ['All subscribers', 'Everyone booked on the events of all calendars'];
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.title'] = 'Sign-in sheet';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.print'] = 'Print';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.showAnswers'] = 'Show their answers';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.hideAnswers'] = 'Hide their answers';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.summary'] = '%s bookings, %s places';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.name'] = 'Name';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.email'] = 'E-mail address';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.places'] = 'Places';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.answers'] = 'Answers';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.signature'] = 'Signature';
$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_register.waiting'] = 'Waiting list';
$GLOBALS['TL_LANG']['tl_calendar']['reports.from'] = 'From month';
$GLOBALS['TL_LANG']['tl_calendar']['reports.to'] = 'To month';
$GLOBALS['TL_LANG']['tl_calendar']['reports.calendar'] = 'Calendar';
$GLOBALS['TL_LANG']['tl_calendar']['reports.download'] = 'Download as CSV';
$GLOBALS['TL_LANG']['tl_calendar']['reports.help'] = 'One row for each month and course. Months are the month the event starts.';
$GLOBALS['TL_LANG']['tl_calendar']['reports.month'] = 'Month';
$GLOBALS['TL_LANG']['tl_calendar']['reports.course'] = 'Course';
$GLOBALS['TL_LANG']['tl_calendar']['reports.events'] = 'Events';
$GLOBALS['TL_LANG']['tl_calendar']['reports.places'] = 'Places';
$GLOBALS['TL_LANG']['tl_calendar']['reports.booked'] = 'Booked';
$GLOBALS['TL_LANG']['tl_calendar']['reports.fill'] = 'Filled';
$GLOBALS['TL_LANG']['tl_calendar']['reports.waiting'] = 'Waiting list';
$GLOBALS['TL_LANG']['tl_calendar']['reports.attended'] = 'Attended';
$GLOBALS['TL_LANG']['tl_calendar']['reports.missed'] = 'Did not attend';
$GLOBALS['TL_LANG']['tl_calendar']['reports.missedRate'] = 'Did not attend (%)';
$GLOBALS['TL_LANG']['tl_calendar']['reports.cancelled'] = 'Cancelled by booker';
$GLOBALS['TL_LANG']['tl_calendar']['reports.rating'] = 'Feedback';
$GLOBALS['TL_LANG']['tl_calendar']['reports.ratingCount'] = 'Feedback answers';
$GLOBALS['TL_LANG']['tl_calendar']['reports.total'] = 'Total';
$GLOBALS['TL_LANG']['tl_calendar']['reports.empty'] = 'No events with bookings in this period.';
$GLOBALS['TL_LANG']['tl_calendar']['reports.notes'] = 'Places: the limit of events that have one. Filled: booked places of those events. Waiting list: places over the limit. Attended and did not attend: only events where someone was marked as attended. Cancelled: only cancellations the booker made themselves.';
$GLOBALS['TL_LANG']['tl_calendar']['reports.noShows'] = 'Repeat no-shows';
$GLOBALS['TL_LANG']['tl_calendar']['reports.noShowsHelp'] = 'People who booked and did not attend at least %s events in the last 12 months. Only events where attendance was recorded count, and never the waiting list.';
$GLOBALS['TL_LANG']['tl_calendar']['reports.noShowsNone'] = 'Nobody has missed that many events.';
$GLOBALS['TL_LANG']['tl_calendar']['reports.person'] = 'Person';
$GLOBALS['TL_LANG']['tl_calendar']['reports.missedCount'] = 'Missed';
$GLOBALS['TL_LANG']['tl_calendar']['reports.lastMissed'] = 'Last missed event';
$GLOBALS['TL_LANG']['tl_calendar']['reports.history'] = 'All their bookings';
$GLOBALS['TL_LANG']['tl_calendar']['person.date'] = 'Date';
$GLOBALS['TL_LANG']['tl_calendar']['person.course'] = 'Course';
$GLOBALS['TL_LANG']['tl_calendar']['person.event'] = 'Event';
$GLOBALS['TL_LANG']['tl_calendar']['person.calendar'] = 'Calendar';
$GLOBALS['TL_LANG']['tl_calendar']['person.outcome'] = 'Attended';
$GLOBALS['TL_LANG']['tl_calendar']['person.payment'] = 'Payment';
$GLOBALS['TL_LANG']['tl_calendar']['person.export'] = 'Export';
$GLOBALS['TL_LANG']['tl_calendar']['person.empty'] = 'No bookings found.';
$GLOBALS['TL_LANG']['tl_calendar']['person.summary'] = '%s bookings, %s attended, %s did not attend.';
$GLOBALS['TL_LANG']['tl_calendar']['person.flag'] = 'Did not attend %s events in the last 12 months.';
$GLOBALS['TL_LANG']['tl_calendar']['person.outcome.attended'] = 'Attended';
$GLOBALS['TL_LANG']['tl_calendar']['person.outcome.missed'] = 'Did not attend';
$GLOBALS['TL_LANG']['tl_calendar']['person.outcome.waiting'] = 'Waiting list';
$GLOBALS['TL_LANG']['tl_calendar']['person.outcome.notRecorded'] = 'Not recorded';
$GLOBALS['TL_LANG']['tl_calendar']['person.outcome.upcoming'] = 'Upcoming';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.history'] = 'All their bookings';
$GLOBALS['TL_LANG']['tl_calendar']['subscribers.missed'] = 'missed %s';
$GLOBALS['TL_LANG']['tl_calendar']['export.download'] = 'Download';
$GLOBALS['TL_LANG']['tl_calendar']['export.xlsx'] = 'Excel';
$GLOBALS['TL_LANG']['tl_calendar']['export.csv'] = 'CSV';
$GLOBALS['TL_LANG']['tl_calendar']['export.ods'] = 'OpenDocument';
$GLOBALS['TL_LANG']['tl_calendar']['export.pdf'] = 'PDF';
$GLOBALS['TL_LANG']['tl_calendar']['export.print'] = 'Print or save as PDF';
$GLOBALS['TL_LANG']['tl_calendar']['export.printHelp'] = 'In the print dialog, choose "Save as PDF" as the printer.';
$GLOBALS['TL_LANG']['tl_calendar']['reports.period'] = 'From %s to %s';
