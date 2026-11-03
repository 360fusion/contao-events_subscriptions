# Changelog

This fork follows Codefog's `master` (last merged: `9cb728cb`, "Fix the backend forms not working in Contao 5.7").

## 3.4.0-360

Security and reliability fixes from a code audit and a security review:

- Names and answers a guest edits on the booking page can no longer carry markup into the back end lists (stored XSS);
  back end labels escape names and e-mail addresses.
- The event, calendar and group exports no longer let a name such as `=HYPERLINK(...)` run as a formula in Excel.
- Back end tools check the user's calendar permissions: bookings, exports, sign-in sheet, notifications, overview,
  subscribers, upcoming events, payments (list, refund, mark as paid) and old booking data only for calendars the user may use.
- Old booking data: only events that really ended before the cutoff can be changed; the default action is anonymise, not
  delete; anonymising member bookings also detaches the member.
- Payments: a payment made after the hold ran out is recorded as paid and logged (it used to be ignored); the hold can no
  longer overwrite a payment that was just paid; a second "pay" link does not lose a payment made on the first; the Stripe
  payment page closes with the hold; provider keys are no longer copied into the version history.
- The e-mail cancel link follows the calendar's cancel deadline; booking tokens come from a secure random source.
- Reminders are not sent for unpaid, failed or refunded bookings, and no longer an hour early.
- Booking export: the end date includes that whole day. Test notification to a member: language is read correctly.
- Waiting list: when an unpaid card booking expires, the next people move up and get the promotion e-mail. Several people
  move up when several places free up, and bookings made in the same second keep a fixed order.
- Refunded bookings are left out of the trainer pack, the sign-in sheet and the thank-you e-mail.
- E-mail test: only uses sample data from calendars the user may see. Check-in needs access to the booking's calendar.
- After a booker has attended, their name on the certificate can no longer be changed. Export files are deleted after the
  download. The default e-mails an administrator deleted are not created again by the next update.

## 3.3.0-360

- Ready-made e-mails for every e-mail type: confirmation, cancellation, place from the waiting list, reminder and new
  event invitation (plus the trainer pack and thank-you e-mails).
- People booked on the waiting list see "You are on the waiting list" after booking. New "Waiting list confirmation" page
  setting on the booking modules.
- Subscribers: a Status filter for bookings with a place or the waiting list.

## 3.2.0-360

- Contao 5.7 fixes: cron jobs run (reminders, trainer packs, thank-yous, payment expiry, data retention), waiting list
  promotion when the oldest booking is cancelled, the member group tool, booking messages.
- Paid bookings: Stripe, PayPal, Mollie, GOV.UK Pay, pay by invoice or purchase order, free e-mail domains, a payments
  list with refunds, a hold time for unpaid card checkouts.
- Events › Settings: pages for General, Payments, E-mails, Certificates and Before and after events; tiles for
  Registration fields, Venues, Trainers, E-mail templates, Reports, Health check and old booking data.
- Running the day: attendance, QR check-in, sign-in sheet, certificates, trainer pack e-mail, thank-you e-mail with
  feedback link, "manage my booking" page for bookers.
- Reports with CSV, Excel, ODS and print/PDF downloads; repeat no-shows; person history.
- Venues and locations; trainers.
- Registration fields managed in the back end; a tag that lists every answer in e-mails.
- Event groups, calendar groups, booking defaults per group; one booking per course; allowed e-mail domains;
  unique guest e-mails.
- Option to show Payments, Venues and Trainers in the left menu.
- English and German labels.
