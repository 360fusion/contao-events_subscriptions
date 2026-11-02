# Backend interface – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [**Backend interface**](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Event list view

After enabling the subscriptions in the calendar settings each event gains a new button. It leads
to the view of subscription list of that event. 

![](images/basics-subscriptions.png)


## Subscription list view

The subscription list view allows you to create, manage and delete the records. At the top of the view
there is also displayed an information about the total number of subscription to this event as well as
the note about limit, if any.

![](images/backend-subscriptions.png)


### Create multiple subscriptions from member group 

From version 2.11 it is possible to create multiple member subscriptions just by selecting the member groups. 
Only the members with active accounts will be subscribed to an event!

![](images/backend-create-from-member-group.png)


### Export subscriptions

The subscriptions of each event or calendar can be exported to the CSV or Excel file. Fore more information on how to alter 
the exported data please check the [Developers](08-developers.md) section.

> To export data in Excel format you have to install the [phpoffice/phpspreadsheet](https://packagist.org/packages/phpoffice/phpspreadsheet) package. 

![](images/backend-subscriptions-export.png)


## Sending notifications per event

As of version 2.15, you can send the notifications manually per every event:

![](images/backend-notifications.png)

## Upcoming events and subscriptions

The calendar list shows a summary at the top: the number of calendars, live events (published events that have not
ended yet, in calendars with subscriptions enabled) and subscriptions to those events. Its link opens a page listing
all live events of all calendars by date, with their subscriptions, places and waiting list, and links to each
event's subscriptions.

The upcoming events page can be filtered by event title, calendar, calendar group, start date range and places
(all events, events with places left, or full events). The line above the list counts the events and taken places
that match the filters.

## Calendar groups

Enable "Calendar groups" in System › Settings to group calendars in the backend. A "Calendar groups" button then
appears above the calendar list. Create a group and tick its calendars, or pick the group in a calendar's settings;
each calendar belongs to one group. With groups enabled:

- the calendar list is shown under group headings,
- the upcoming events page can be filtered by group,
- each group has an export button that exports the subscriptions of all its calendars into one CSV/Excel file.

## Event groups

A calendar can be used as a category and its event groups as the courses within it, so a new course does not need
a new calendar. Click the folder icon of a calendar to manage its event groups (title, alias, description, published),
then pick the group in each event's settings. The event group can be used as a filter in the calendar's event list,
on the upcoming events page, and it is added as a column to the exports.

Event groups have the same booking settings as an event (override, custom e-mail domains, custom notifications).
They apply to the events of the group that do not override them, so the order is: event, event group, calendar.
Each event group has an export button that exports the subscriptions of all its events into one file.

## Attendance

Each subscription in the subscription list has an "Attended" toggle. Tick it once the person attended; the
subscription list can be filtered by it and the exports have an "Attended" column.

## Permissions of new calendars

When an administrator creates a calendar, the "Back end permissions" of the new calendar are pre-selected with
every user group that can access the events module, so editors can manage the new calendar straight away.
Untick a group before saving to keep the calendar from it.


## The Events menu

The extension groups everything about events in an "Events" group of the back end menu. Each entry can be granted
separately in the user group permissions:

- **Calendars**: the calendars, their events and bookings (the core events module, same URL `do=calendar`)
- **Upcoming events**: the upcoming events of all calendars with filters and search
- **Subscribers**: everyone booked, across all calendars, with filters and search (each calendar has a person icon
  for its own subscribers)
- **Payments**: payments, refunds and invoices (when switched on)
- **Settings**: a "Booking settings" section with a tile for each of the five settings pages, the registration
  fields, the venues and trainers (once switched on) and the payment providers (once payments are on), and a "Tools and data" section with the e-mail templates,
  the reports, the health check, old booking data and (when switched on) the calendar groups
  Every page opened from a tile has a Back button (to Settings, or to the Payments page for the provider list and the
  Pay by invoice page).
  Every tile has a small bar along the bottom: the state on the left (On or Off, "All fine" or "Needs
  attention", "Signature set") and a number on the right (fields, venues, trainers, providers, payments, attended, e-mails, checks or
  events).

### Settings

- **Booking settings**, in five pages:
  - **General**: the feature switches (attendance, calendar groups, one booking per course); a calendar whose
    booking settings (places, deadlines, registration fields, e-mail domains, e-mails, reminders, price) every new
    calendar starts with; the number of missed events for the repeat no-show list; the booking form texts (empty
    fields use the default texts, shown in grey); and removing the personal data of old bookings: once a day,
    bookings of events that ended more than the chosen number of months ago are anonymised (places, attendance and
    payment amounts are kept) or deleted. Anonymising also clears text columns of the subscription table that are
    no longer defined, e.g. left over from an older extension.
  - **Payments**: switches payments on. Once on, the **payment types** show as tiles right under the switch: each card
    provider (Stripe, PayPal, Mollie, GOV.UK Pay) with Active, Inactive or "Add account details" and Test or Live with
    its currency, a **Pay by invoice** tile, and a dashed tile to add a provider. Click a tile to change it. The page
    also sets how long an unpaid card booking holds its place (30 to 1440 minutes, default 30), and whether
    **Payments is shown in the left menu** (on by default; untick it and the payments list opens from a Payments tile in
    the "Tools and data" section of the Settings page instead). The Payments menu item and the price
    settings appear once payments are on.
    - A **provider** can be switched off without deleting it (the Active tick, or the toggle button in its list). It is
      then not offered on new bookings, but its settings stay and payments already made with it can still be refunded
      and updated. A provider can only be made active once its account details are entered (Stripe: secret key;
      PayPal: client ID and secret; Mollie and GOV.UK Pay: API key); a new provider starts inactive.
    - **Pay by invoice** has its own page: an Active switch (on unless switched off) and the instructions people see on
      the booking form, also available as the e-mail tag `##payment_invoice_text##`.
    - An event with a price and payment methods, none of which can be offered right now (the provider is inactive or
      has no details, invoice is switched off, or Payments itself is switched off), takes no bookings until one is
      available again: the booking form says "Booking is not available at the moment" (templates: the variable
      `isPaymentUnavailable`, text `events_subscriptions.paymentUnavailableEvent`). It never turns into a free event;
      set its price to 0 to make it free. The health check lists these events. Events without a price are not
      affected. With Payments off, prices and payment fields do not show on the booking form at all.
  - **E-mail signatures**: the sender of all event e-mails (saving updates them) and the signature for the `##signature##` tag.
  - **Certificates**: certificates of attendance on or off, the organisation name and who signs.
  - **Before and after events**: the trainer pack and after-the-event e-mails, and the hour the trainer pack goes out.

  Each page saves only its own settings. Each is its own back end module (`event_settings`,
  `event_settings_payments`, `event_settings_emails`, `event_settings_certificates`, `event_settings_mailings`),
  so a user group can be given only some of them.
- **Old booking data**: the past events whose bookings still have personal data, older than a chosen number of
  months (the retention setting by default). Tick events, or all of them, and delete or anonymise their bookings.
  The events themselves are kept.
- **Health check**: e-mails that are used but send nothing, calendars without a confirmation e-mail, upcoming events
  without a course or a place limit, paid events that cannot take payments, courses without upcoming dates and old
  bookings with personal data, each with links to fix them.

## Running the day

- **Sign-in sheet.** The list of events has a "Sign-in sheet" button for calendars with bookings: a printable page
  with everyone booked in alphabetical order, their e-mail address, places and an empty column to sign, a few blank
  lines for people who turn up without a booking, and the waiting list underneath. "Show their answers" adds their
  registration field answers.
- **QR check-in.** The booking page of the booker shows a QR code from the moment they book until the end of the event day. Scanning it with a phone
  that is signed in to the back end opens a page with the name and a "Check in" button, which marks the booking as
  attended. Anyone who is not signed in to the back end gets a "staff only" page, so nobody can check themselves in.
- **Trainer e-mail.** Events and event groups have a "Trainer e-mail address" for the trainer pack (see Notifications).

## Reports

Events › Settings › Reports shows one row for each month and course: events, places, booked, how full, the waiting list,
attended and did not attend, cancellations by the booker and the average feedback rating, with a month range, a
calendar filter and downloads as Excel (.xlsx), CSV or OpenDocument (.ods), or as a PDF: the PDF button opens a
print-ready page and the browser makes the PDF (choose "Save as PDF" as the printer). Numbers stay numbers in the
spreadsheet files, with a totals row at the end, and a text that starts with `=`, `+`, `-` or `@` is never run as a
formula. The same five download buttons are on the history page of a person.

- *Filled* counts the places of events that have a limit. Places over the limit are the waiting list.
- *Attended* and *Did not attend* only count events where someone was marked as attended, so events where
  attendance was never recorded are not reported as everybody missing.
- *Cancelled by booker* counts cancellations made with the unsubscribe link or "Manage my booking", from the time the
  reports were added. Bookings deleted by hand in the back end are not counted.

**Repeat no-shows** lists people who booked and did not attend at least N events in the last 12 months (3 by default,
Events › Settings › Booking settings › General). Waiting list places never count. The same people are marked "missed N"
in the Subscribers list. Click a name in the Subscribers list or the no-show list for **all their bookings** (outcome
for each, with an export). The history only shows the calendars the user may access.

## Venues and locations

Switch it on in Events › Settings › Booking settings › General. Events › Settings › Venues and locations then keeps the places
events are held: name, street, town, postcode, "Getting there" (directions and parking), "Accessibility", a map link, and
whether the place is still offered on events. The list shows the address and how many events use each place.

Every event gets a **Venue** choice above its location. When you save an event with a venue, its *location* is set to the
venue's name and its *address* to "street, town, postcode". They are the event's normal location and address fields, so
every event list, the event page, the calendar file (ICS) and the `##event_location##` and `##event_address##` e-mail
tags show the venue without changing any template. Those two fields are read-only while a venue is chosen; empty the
venue choice to type them yourself again.

- Editing a venue updates the location and address of all its events.
- Deleting a venue keeps the location and address its events have now, and only removes the link.
- Unticking *Available for events* hides the place from the Venue choice; events that already use it keep it.
- The event page shows the directions, the accessibility information and a "View map" link under the location (the
  site's own `event_full` template needs no change). The e-mail tags are `##event_venue_directions##`,
  `##event_venue_accessibility##` and `##event_venue_map##`.
- Switching venues off hides the tile and the Venue choice; events keep their location and address.
- Tick **Show Venues and locations in the left menu** under the Venues switch (off by default) to add a link in the Events menu. The tile on the Settings page stays either way.

## Trainers

Switch it on in Events › Settings › Booking settings › General. Events › Settings › Trainers then keeps the people who run events:
name, e-mail address and whether they are still offered. The list shows how many events and courses use each trainer.

Events and courses get a **Trainer** choice above the *Trainer e-mail address*. The trainer pack e-mail goes to:

1. the trainer chosen on the event, otherwise the e-mail address typed on the event,
2. then, if the event has neither, the trainer chosen on its course, otherwise the address typed on the course.

On the same event or course, a chosen trainer comes before a typed address. Use the typed address for someone who is not
in the list. The address is read when the pack is sent, so changing a trainer's e-mail address changes it for every event
and course that uses them. Deleting a trainer copies their address into the typed address of the events and courses that
used them, so their packs still go out. Unticking *Available for events* hides a trainer from the choice; events that
already use them keep them. Switching Trainers off hides the tile and the choice; a trainer already chosen is still
used. The trainer's name is the e-mail tag `##event_trainer_name##` (empty when only an address was typed).

Tick **Show Trainers in the left menu** under the Trainers switch (off by default) to add a link in the Events menu. The tile on the Settings page stays either way.

## Settings

- *One booking per course* (off by default): stops someone booking a second upcoming date of a course they are
  already booked on, matched by e-mail address (guest and member bookings, waiting list included).
- *Certificates of attendance* (on by default, needs Attendance): name of the organisation and who signs.
