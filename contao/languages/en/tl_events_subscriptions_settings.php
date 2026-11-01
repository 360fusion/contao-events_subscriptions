<?php

$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTemplateCalendar'] = ['New calendars copy the booking settings of', 'A new calendar starts with all booking settings of this calendar (places, deadlines, registration fields, e-mail domains, e-mails, reminders, price). They can be changed afterwards.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsRetention'] = ['Remove personal data of old bookings', 'Once a day, remove the names, e-mail addresses and answers of bookings for events that ended a while ago.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsRetentionMonths'] = ['Keep booking details for (months)', 'Months after the end of the event, e.g. 24.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsRetentionAction'] = ['Then', 'Anonymising keeps the booking (places, attendance, payment amount) without the personal data. Deleting removes the booking.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionActions'] = ['anonymise' => 'Anonymise the booking', 'delete' => 'Delete the booking'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['defaults_legend'] = 'New calendars';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retention_legend'] = 'Keeping booking details';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsPayments'] = ['Payments', 'Events can have a price, paid by card or invoice. Shows the Payments menu item and the price settings.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsAttendance'] = ['Attendance', 'Mark who attended in the bookings list, with a filter and an export column.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsCalendarGroups'] = ['Calendar groups', 'Group calendars: list them under their group, filter the upcoming events by group and export a group\'s bookings in one file.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsSenderName'] = ['Sender name', 'The name people see in their inbox, for example "South Tees Events". Saving replaces the sender name of every event e-mail. Leave it empty to keep each e-mail as it is.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsSenderAddress'] = ['Sender address', 'The address the e-mails are sent from. It must be one your mail server may send from. Saving replaces the sender address of every event e-mail. Leave it empty to keep each e-mail as it is.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsSignature'] = ['Signature', 'A block of text you set once, like an Outlook signature: your team name and contact details. It is not added by itself. While editing an e-mail, click Insert tag › Signature where it should go, usually at the end. Change it here and every e-mail that uses it changes. Line breaks are kept.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTextIntro'] = ['Above the booking form', 'Leave empty to use the default text shown in grey.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTextClosed'] = ['Booking has closed', 'Leave empty to use the default text shown in grey.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTextFull'] = ['Event is full', 'Leave empty to use the default text shown in grey.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTextWaitingList'] = ['Event is full, waiting list open', 'Leave empty to use the default text shown in grey.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTextConfirmation'] = ['After booking', 'Shown after booking when no thank-you page is set. Leave empty to use the default text shown in grey.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['features_legend'] = 'Features';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['emails_legend'] = 'Sender and signature';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['wording_legend'] = 'Booking form texts';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionOlderThan'] = 'Events that ended more than';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionMonths'] = 'months ago';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionPerPage'] = 'Per page';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionHelp'] = 'Only events whose bookings still have personal data are listed. The events themselves are kept.';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionSummary'] = '%s events with %s bookings that still have personal data, older than %s months';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionSelectPage'] = 'Select all on this page';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionCourse'] = 'Course / event';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionDate'] = 'Date';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionCalendar'] = 'Calendar';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionBookings'] = 'Bookings';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionAllMatching'] = 'All %s events, not only the ticked ones';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionApply'] = 'Apply';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionNothingSelected'] = 'Please tick the events first.';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionConfirm'] = '%action for %count events? This cannot be undone.';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionDone'] = ['delete' => 'Deleted %s bookings of %s events.', 'anonymise' => 'Anonymised %s bookings of %s events.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsOneBookingPerCourse'] = ['One booking per course', 'Stop someone booking a second date of a course they are already booked on (matched by e-mail address). Only upcoming dates count, and only for events that belong to a course.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTrainerNotification'] = ['Trainer pack e-mail', 'Sent the day before each event to the trainer\'s e-mail address (set on the event or its course), with the list of everyone booked. Create it under Settings › E-mail templates (type: Trainer pack). Leave empty to switch off.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsTrainerHour'] = ['Send the trainer pack at (hour)', 'Hour of the day, 0 to 23. Default 16.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsFollowUpNotification'] = ['After-the-event e-mail', 'Sent the day after to everyone who attended, with their certificate and feedback links. Create it under Settings › E-mail templates (type: After the event). Leave empty to switch off.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsCertificate'] = ['Certificates of attendance', 'People marked as attended can open a printable certificate from their booking page or the e-mail link. Needs Attendance.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsCertificateOrganisation'] = ['Organisation name on the certificate', 'Shown at the top of the certificate, for example: South Tees Hospitals NHS Foundation Trust'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsCertificateSignedBy'] = ['Signed by', 'Shown at the bottom of the certificate next to the date it was issued, for example: Education and Training Team'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsNoShowLimit'] = ['Repeat no-shows from', 'In Events › Settings › Reports, list people who booked and did not attend this many events in the last 12 months.'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['mailings_legend'] = 'Before and after the event';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['certificate_legend'] = 'Certificate';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['reports_legend'] = 'Reports';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['sections'] = ['event_settings' => 'General', 'event_settings_payments' => 'Payments', 'event_settings_emails' => 'E-mail signatures', 'event_settings_certificates' => 'Certificates', 'event_settings_mailings' => 'Before and after events'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['setupSections'] = ['settings' => 'Booking settings', 'tools' => 'Tools and data'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['setupStatus'] = ['on' => 'On', 'off' => 'Off', 'mailings' => '%s of 2 on'];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['payments_legend'] = 'Payments';
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['signaturePlaceholder'] = "Kind regards,\nThe Education Team\nTel: 01234 567890\neducation@example.nhs.uk";
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['statusTitle'] = 'How things stand now';

// Above each settings page: what it is for, how it works and where to go
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['explain'] = [
    'default' => [
        ['p', 'These settings apply to the whole booking system. Settings for one calendar, course or event (places, deadlines, waiting list, registration fields, reminders) are set where you edit that calendar, course or event in {calendars}.'],
        ['ul', ['<strong>Features</strong>: <em>Attendance</em> adds the Attended tick to bookings, a filter and an export column. It is needed for check-in, certificates and the did-not-attend figures in {reports}. <em>Calendar groups</em> lists calendars under their group. <em>One booking per course</em> stops someone booking two dates of the same course.', '<strong>Venues and locations</strong>: tick it to keep a list of the places events are held, under {venues}. Each event then has a <em>Venue</em> choice that fills in its location and address on the website, with directions, accessibility and a map link on the event page.',
   '<strong>New calendars</strong>: choose a calendar to copy. Every new calendar then starts with its booking settings and can be changed afterwards.', '<strong>Reports</strong>: how many missed events make someone a repeat no-show. They are listed in {reports} and marked in {subscribers}.', '<strong>Booking form texts</strong>: the wording people see on the event page. Leave a box empty to use the default text, shown in grey.', '<strong>Keeping booking details</strong>: tick it to remove names, e-mail addresses and answers of old bookings automatically, by anonymising or deleting them. You can also review and delete them by hand at any time in {retention}.']],
    ],
    'payments' => [
        ['p', 'Payments let a calendar, course or event have a price. People pay by card online, or give an invoice, purchase order or cost centre reference. Everything below stays hidden until you tick the box and save.'],
        ['h', 'What you get when it is on'],
        ['ul', ['<strong>Payment providers</strong>: connect Stripe, PayPal, Mollie or GOV.UK Pay in {providers}. Each has a test mode, a live mode and a currency (pounds by default). Card details are entered on the provider\'s own page, never on this site.', '<strong>Prices</strong>: when you edit a calendar, course or event in {calendars}, set the price, the payment methods (card, invoice), the provider and the e-mail domains that book free, such as nhs.net. An event uses its course\'s price and a course uses its calendar\'s.', '<strong>Payments list</strong>: a Payments item appears in the Events menu with every payment, including those of cancelled bookings: who, which event, amount, provider and status.', '<strong>Refunds</strong>: an administrator can refund a card payment in full from the Payments list, and the money goes back through the provider. Cancelling a booking never refunds by itself.', '<strong>Invoices</strong>: bookings paid by invoice or purchase order are confirmed straight away. Mark them as paid in the Payments list when the money arrives.', '<strong>Waiting list</strong>: nobody pays while on a waiting list. When someone gets a place they receive a link to pay (<code>##payment_link##</code>). An unpaid card booking holds its place for 30 minutes and is then cancelled.']],
        ['h', 'How to set it up'],
        ['ol', ['Tick <em>Payments</em> below and save.', 'Add a provider in {providers}. Copy the webhook address shown there into the provider\'s own settings, so bookings are confirmed even if the payer closes the browser. Start with the provider\'s test keys.', 'Set a price on a calendar, course or event in {calendars}.', 'Make a test booking with the provider\'s test card and check it in the Payments list. Switch the provider to live only when that works.']],
    ],
    'emails' => [
        ['p', 'These settings are shared by every event e-mail: booking confirmations, cancellations, reminders, the trainer pack and the thank-you e-mail. Set them once here instead of in each e-mail.'],
        ['ul', ['<strong>Writing the e-mails</strong>: the e-mails themselves are written and changed in {emails}. Each has a name, when it is sent, a subject and a message. Click a tag to insert it where the cursor is.', '<strong>Sender</strong>: who the e-mails come from. Saving a sender name or address here sets it on all event e-mails at once.', '<strong>Signature</strong>: a saved block of text like an Outlook signature, for your team name and contact details. It is not added by itself: while editing an e-mail, click Insert tag › Signature where it should go, usually at the end. Change the signature here and every e-mail that uses it changes.']],
    ],
    'certificates' => [
        ['p', 'A certificate of attendance is a printable page with the person\'s name, the course, the date, the place and, for a single-day event with times, the hours. It can be printed or saved as a PDF from the browser.'],
        ['h', 'How it works'],
        ['ol', ['Record who attended: tick <em>Attended</em> on the booking in {subscribers}, scan the QR code on the person\'s booking page at the door, or use the sign-in sheet and tick people afterwards.', 'People marked as attended can open their certificate from their <em>Manage my booking</em> page and from the e-mail tag <code>##certificate_link##</code>.', 'The e-mail sent the day after the event already includes the link: choose it in {mailings}, and change its wording in {emails}.']],
        ['p', 'Certificates need <em>Attendance</em> to be switched on in {general}. The organisation name is shown at the top of the certificate and <em>Signed by</em> at the bottom.'],
    ],
    'mailings' => [
        ['p', 'The site can send two e-mails by itself around an event. Both stay off until you choose an e-mail for them below.'],
        ['ul', ['<strong>Trainer pack</strong>: the day before an event, the trainer gets the list of everyone booked with their answers (the waiting list is left out). It goes to the <em>Trainer e-mail address</em> of the event, or of its course if the event has none: set it when you edit the event or course in {calendars}. Events without one send nothing.', '<strong>After the event</strong>: the day after, everyone marked as attended gets a thank-you with their certificate and a feedback link. Only events that ended in the last 3 days are included.']],
        ['h', 'How to set it up'],
        ['ol', ['Create the e-mail in {emails}: New e-mail, then choose the type <em>Trainer pack the day before</em> or <em>After the event</em>.', 'Choose it below and save.', 'For the trainer pack, enter a trainer e-mail address on your events or courses.']],
        ['p', 'Both are sent by the site\'s hourly scheduled tasks (cron), so on a live server cron must be running. Each e-mail is sent once for each event or booking.'],
    ],
];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['status'] = [
    'senderOne' => 'All %s event e-mails are sent from %s.',
    'senderMany' => 'Your %s event e-mails currently use %s different senders: %s<br>Saving a sender name or address below sets it on all of them.',
    'signatureUse' => '%s of your %s e-mails include the signature.',
    'signatureNone' => 'None of your %s e-mails includes the signature yet. Add it while editing an e-mail: Insert tag › Signature.',
    'paymentsOn' => 'Payments are on. %s payment provider(s) are set up.',
    'paymentsOnNoProvider' => 'Payments are on, but no payment provider is set up yet: card payments are not offered until you add one in {providers}.',
    'paymentsOff' => 'Payments are off. Nothing about prices or payments is shown anywhere.',
    'attended' => '%s bookings are marked as attended so far.',
    'attendanceOff' => 'Attendance is switched off, so certificates are not available. Switch it on in {general}.',
    'mailingsReady' => 'E-mails ready to choose: %s trainer pack, %s after the event. If a number is 0, create that e-mail in {emails} first.',
    'trainerEvents' => '%s upcoming events have a trainer e-mail address, so would send a trainer pack.',
];
$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['eventsSubscriptionsVenues'] = ['Venues and locations', 'Keep a list of the places events are held. Adds Venues and locations to the Events menu and a Venue choice on every event, which fills in the location and address shown on the website.'];
