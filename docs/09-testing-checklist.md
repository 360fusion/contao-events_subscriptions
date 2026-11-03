# Testing checklist for a person

Use a test calendar, test events and addresses you control (for example `you+test1@yourdomain`). Tick each line when it
works. Write down anything odd: what you did, what you expected, what you saw.

Local sites: send e-mails to a mail catcher such as Mailpit, never to real people.

## 1. Set up

- [ ] Events › Settings opens and every tile opens its page.
- [ ] Booking settings › General: switch on Attendance, Venues and Trainers; save; the tiles appear.
- [ ] A calendar has *Enable subscriptions* ticked, with places, a booking deadline and a reminder.
- [ ] The reader page shows the booking form (First name, Last name, E-mail address, any registration fields).

## 2. Booking, as a guest

- [ ] Book with valid details: the thank-you text shows and the confirmation e-mail arrives with every answer listed.
- [ ] Leave a required field empty: a clear message, nothing saved.
- [ ] Book again with the same e-mail: refused with a clear message.
- [ ] Fill the event: the next person sees the waiting list text, is booked as waiting, and the e-mail says so.
- [ ] After the booking deadline: "Registration has closed" and no form.
- [ ] On a phone: the form is readable, nothing scrolls sideways, buttons are easy to tap.

## 3. Manage my booking

- [ ] The link in the e-mail opens the booking page.
- [ ] Change the name and an answer: saved, and visible in the back end.
- [ ] Cancel: the cancellation e-mail arrives, the place is free again.
- [ ] A cancellation moves the first person on the waiting list up and sends them the "place available" e-mail.
- [ ] A made-up or shortened link shows "not found", not an error page.

## 4. Payments (use the provider's test mode and test cards only)

- [ ] A priced event shows the price. Paying by card goes to the provider and back, and the booking shows Paid.
- [ ] Abandoning the payment frees the place after the hold time.
- [ ] Pay by invoice asks for a purchase order and confirms straight away.
- [ ] A refund from the Payments list marks the booking Refunded.
- [ ] Payments switched off: a priced event says it cannot be booked and shows no form.

## 5. Running the day

- [ ] The sign-in sheet prints, in alphabetical order, with the waiting list underneath.
- [ ] Scanning the QR code on the booking page while signed in to the back end offers *Check in*; signed out it says staff only.
- [ ] After check-in the certificate page opens with the right course, date and hours; a person not checked in gets "not found".
- [ ] The feedback page saves a rating once; a second visit only says thank you.

## 6. E-mails

- [ ] Each e-mail in Events › Settings › E-mail templates can be sent as a test to your own address only.
- [ ] The trainer pack arrives the day before, once, to the right trainer.
- [ ] The thank-you e-mail goes only to people checked in.
- [ ] Reminders go out the chosen number of days before, once.

## 7. Back end

- [ ] Subscribers: search, filter by status and by calendar.
- [ ] Reports: a month's figures look right, and Excel, CSV, OpenDocument and PDF download and open.
- [ ] A booking with a name like `=1+1` or `@test` shows as text in the CSV and Excel downloads, not as a calculation.
- [ ] Old booking data: choosing anonymise or delete asks to confirm and only touches the events you ticked.
- [ ] An editor who may not see a calendar cannot open its bookings, payments or reports by changing the address.
