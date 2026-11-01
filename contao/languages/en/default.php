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
    'events_subscriptions_trainer' => 'Trainer pack the day before',
    'events_subscriptions_followUp' => 'After the event (certificate, feedback)',
    'events_subscriptions_subscribe' => 'When someone books',
    'events_subscriptions_unsubscribe' => 'When someone cancels',
    'events_subscriptions_listUpdate' => 'When someone gets a place from the waiting list',
    'events_subscriptions_reminder' => 'Reminder before the event',
    'events_subscription_event' => 'Sent by hand from the subscriptions list',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTagGroups'] = ['attendees' => 'Attendees', 'other' => 'Other', 'booking' => 'Booking', 'event' => 'Event', 'links' => 'Links', 'payment' => 'Payment'];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTags'] = [
    'booking_link' => 'Manage my booking link',
    'certificate_link' => 'Certificate link',
    'feedback_link' => 'Feedback link',
    'attendee_list' => 'List of everyone booked',
    'attendee_count' => 'Number of bookings',
    'attendee_places' => 'Number of places booked',
    'subscription_details' => 'All booking details',
    'signature' => 'Signature',
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

// Events › Setup › Health check: [problem, all fine]
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.health'] = [
    'emails' => ['%s e-mails are used by calendars, groups or events but send nothing (not set up or switched off)', 'All e-mails in use send something'],
    'noConfirmation' => ['%s calendars take bookings without a confirmation e-mail', 'Every calendar with bookings sends a confirmation e-mail'],
    'noGroup' => ['%s upcoming events are not in a course (event group)', 'All upcoming events are in a course'],
    'noLimit' => ['%s upcoming events have no place limit', 'All upcoming events have a place limit'],
    'noPayment' => ['%s upcoming events have a price but no payment method that can be offered (card needs a payment provider)', 'All paid events can take payments'],
    'emptyGroups' => ['%s published courses have no upcoming dates', 'All published courses have upcoming dates'],
    'retentionOff' => ['Removing old personal data is switched off: %s bookings are of events that ended more than %s months ago', 'No bookings of events older than %2$s months'],
    'retentionDue' => ['%s bookings of events older than %s months are due and will lose their personal data on the next daily run', 'No bookings of events older than %2$s months'],
    'more' => '… and %s more',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.formIntro'] = 'Please register for this event by filling in the form below.';

// The booker's own pages (booking, certificate, feedback, check-in)
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.public'] = [
    'bookingTitle' => 'Your booking',
    'notFound' => 'This link is not valid any more. The booking may have been cancelled.',
    'event' => 'Event',
    'date' => 'Date',
    'place' => 'Location',
    'status' => 'Status',
    'name' => 'Name',
    'statusBooked' => 'Booked',
    'statusWaiting' => 'On the waiting list',
    'checkinTitle' => 'Show this code when you arrive',
    'checkinHelp' => 'The person at the door scans it to mark you as attending.',
    'attendedNotice' => 'You are marked as attended.',
    'certificateOpen' => 'Open your certificate',
    'edit' => 'Change your details',
    'emailLocked' => 'To use another e-mail address, cancel this booking and book again.',
    'save' => 'Save changes',
    'saved' => 'Your details were saved.',
    'required' => 'Please fill in "%s".',
    'invalid' => '"%s" is not valid.',
    'cancel' => 'Cancel my booking',
    'cancelConfirm' => 'Cancel this booking?',
    'cancelled' => 'Your booking for "%s" was cancelled.',
    'cancelClosed' => 'Cancelling online has closed for this event. Please contact the team.',
    'cancelPaid' => 'You have paid for this booking. To ask for a refund, please contact the team after cancelling.',
    'certificateTitle' => 'Certificate of attendance',
    'certificateCertify' => 'This is to certify that',
    'certificateAttended' => 'attended',
    'certificateHours' => '%s hours',
    'certificateIssued' => 'Issued %s',
    'certificatePrint' => 'Print or save as PDF',
    'certificateNone' => 'There is no certificate for this booking. Certificates are available once you are marked as attended.',
    'feedbackTitle' => 'Tell us how it went',
    'feedbackThanks' => 'Thank you for your feedback.',
    'feedbackNotYet' => 'Feedback opens once the event has started.',
    'feedbackRating' => 'Your rating',
    'feedbackRatingMissing' => 'Please choose a rating.',
    'feedbackComment' => 'Anything we should know? (optional)',
    'feedbackSend' => 'Send feedback',
    'rating1' => '1 - Poor',
    'rating2' => '2 - Fair',
    'rating3' => '3 - Good',
    'rating4' => '4 - Very good',
    'rating5' => '5 - Excellent',
    'checkinPage' => 'Check-in',
    'checkinStaffOnly' => 'Staff only. Sign in to the back end, then scan the code again.',
    'signIn' => 'Sign in',
    'checkinButton' => 'Check in',
    'checkinDone' => '%s is checked in.',
    'checkinAlready' => '%s was already checked in.',
    'checkinWaiting' => 'This booking is on the waiting list.',
    'checkinWrongDay' => 'Note: this booking is for %s, not today.',
];
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.noAttendees'] = 'No one has booked.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.sameCourseError'] = 'You are already booked on another date of this course: %s (%s). Please cancel that booking first, using the link in your confirmation e-mail.';
