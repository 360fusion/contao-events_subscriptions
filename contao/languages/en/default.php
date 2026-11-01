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
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribe']               = 'Subscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeWaitingList']    = 'Subscribe to a waiting list';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribe']             = 'Unsubscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeWaitingList']  = 'Unsubscribe from a waiting list';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation']   = 'You have subscribed to the event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'] = 'You have unsubscribed from the event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeNotAllowed']     = 'It is no longer possible to subscribe to this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeNotAllowed']   = 'It is no longer possible to unsubscribe from this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']           = 'waiting list';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.enableReminders']         = 'Send me reminders for this event';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.canSubscribeUntil']       = 'You can subscribe until: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.canUnsubscribeUntil']     = 'You can unsubscribe until: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants']    = 'Number of participants';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsLimit'] = 'You can subscribe up to %d participants to this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailDomainError'] = 'Only e-mail addresses from these domains can subscribe: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailUniqueError'] = 'This e-mail address is already subscribed to this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceeded'] = 'Unfortunately, you cannot subscribe because the maximum number of participants in this event is %d.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceededWaitingList'] = 'Unfortunately, you cannot subscribe because the maximum number of participants in this event is %d, with an extra waiting list of %d slots.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsSubscribeToWaitingList'] = 'You can only be subscribed to the waiting list of this event because the maximum number of participants is %d. Please re-submit the form to subscribe to the waiting list.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname']     = 'First name';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname']      = 'Last name';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email']         = 'E-mail address';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'] = 'Member ID %s is already subscribed to this event!';

/**
 * Export
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'] = [
    'event_id'                 => 'Event ID',
    'event_title'              => 'Event title',
    'event_group'              => 'Event group',
    'event_start'              => 'Event start',
    'event_end'                => 'Event end',
    'subscription_date'        => 'Subscription date',
    'subscription_type'        => 'Subscription type',
    'subscription_waitingList' => 'Waiting list',
    'subscription_attended'    => 'Attended',
    'payment_status'           => 'Payment status',
    'payment_amount'           => 'Amount',
    'payment_method'           => 'Payment method',
    'purchase_order'           => 'Purchase order / cost centre',
    'payment_reference'        => 'Payment reference',
    'subscription_firstname'   => 'Firstname',
    'subscription_lastname'    => 'Lastname',
    'subscription_email'       => 'E-mail address',
    'subscription_numberOfParticipants' => 'Number of participants',
];

$GLOBALS['TL_LANG']['MSC']['events_subscriptions.memberExportHeaderFields'] = [
    'member_id'       => 'Member ID',
    'member_username' => 'Member username',
];

// Registration fields
$GLOBALS['TL_LANG']['MSC']['subscriptionFieldDeleteConfirm'] = 'Delete this field? Answers already given to it are removed when a registration is next saved in the backend. To keep them, disable the field instead.';
// Booking status
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscriptionClosed'] = 'Registration for this event has closed.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.eventFull'] = 'This event is full.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.eventFullWaitingList'] = 'This event is full. You can still join the waiting list.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.placesLeft'] = '%s of %s places left';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.fullyBooked'] = 'Fully booked';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.fullyBookedWaitingList'] = 'Fully booked – waiting list open';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.bookingCloses'] = 'Booking closes: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.bookingClosed'] = 'Booking closed';

$GLOBALS['TL_LANG']['MSC']['eventNavigation.label'] = 'Events';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.all'] = 'All events';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.calendar'] = 'Category';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.group'] = 'Event group';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.month'] = 'Month';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.anyMonth'] = 'Any month';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.filter'] = 'Filter';
$GLOBALS['TL_LANG']['MSC']['eventNavigation.reset'] = 'Reset';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.addToCalendar'] = 'Add to calendar';

// Payments
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'] = ['free' => 'Free', 'pending' => 'Awaiting payment', 'unpaid' => 'Unpaid (waiting list)', 'paid' => 'Paid', 'invoice' => 'To invoice', 'refunded' => 'Refunded', 'failed' => 'Failed'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.price'] = 'Price: %s per person.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.priceFreeFor'] = 'Free for %s e-mail addresses.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentMethod'] = ['How will you pay?', ''];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.purchaseOrder'] = ['Purchase order or cost centre', 'Required when paying by invoice.'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.purchaseOrderRequired'] = 'Please enter a purchase order or cost centre to pay by invoice.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentMessage'] = [
    'paid' => 'Thank you, your payment was received and your place is confirmed.',
    'failed' => 'The payment was not completed, so the booking was cancelled. You can book again.',
    'pending' => 'Your payment has not been completed yet. <a href="%s">Pay now</a> to keep your place.',
    'unpaid' => 'Your payment has not been completed yet. <a href="%s">Pay now</a>.',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentUnavailable'] = 'Card payments are not available at the moment, please try again later or choose another payment method.';

// Events › E-mails
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTypes'] = [
    'events_subscriptions_subscribe' => 'When someone books',
    'events_subscriptions_unsubscribe' => 'When someone cancels',
    'events_subscriptions_listUpdate' => 'When someone gets a place from the waiting list',
    'events_subscriptions_reminder' => 'Reminder before the event',
    'events_subscription_event' => 'Sent by hand from the subscriptions list',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTagGroups'] = ['booking' => 'Booking', 'event' => 'Event', 'links' => 'Links', 'payment' => 'Payment'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags'] = [
    'subscription_firstname' => 'First name',
    'subscription_lastname' => 'Last name',
    'subscription_email' => 'E-mail address',
    'subscription_numberOfParticipants' => 'Number of participants',
    'event_title' => 'Event title',
    'event_startDate' => 'Start date',
    'event_startTime' => 'Start time',
    'event_endDate' => 'End date',
    'event_endTime' => 'End time',
    'event_location' => 'Location',
    'event_address' => 'Address',
    'event_teaser' => 'Teaser',
    'days_before_event' => 'Days until the event',
    'calendar_title' => 'Calendar',
    'event_link' => 'Event page link',
    'event_ics_link' => 'Add to calendar link',
    'unsubscribe_link' => 'Cancel booking link',
    'payment_amount' => 'Amount',
    'payment_status' => 'Payment status',
    'payment_method' => 'Payment method',
    'purchase_order' => 'Purchase order / cost centre',
    'payment_link' => 'Pay now link',
];
