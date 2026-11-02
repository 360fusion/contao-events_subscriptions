# Changelog

This fork follows Codefog's `master` (last merged: `9cb728cb`, "Fix the backend forms not working in Contao 5.7").

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
