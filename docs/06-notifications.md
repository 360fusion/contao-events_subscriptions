# Notifications – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [**Notifications**](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Notification types and their purposes

The notifications are handled by the
[Notification Center](https://github.com/terminal42/contao-notification_center) extension.

1. **Event reminder**

   This notification type is used to send the reminders about the event. You must choose it in the
   calendar settings if you have enabled the reminders.

2. **Event subscribe**

   This notification type is used to confirm the member subscribed to the event. This notification
   does not need to be chosen anywhere, all of this notification types will be sent upon subscription.
   **Note:** this notification is also sent when the user is subscribed to the event in the backend!

3. **Event unsubscribe**

   This notification type is used to confirm the member unsubscribed from the event. This notification
   does not need to be chosen anywhere, all of this notification types will be sent upon unsubscription.
   **Note:** this notification is also sent when the user is unsubscribed from the event in the backend!

4. **Event waiting list promotion**

   This notification type is used to update the member waiting list status. It is sent only if the subscription
   promotes from a waiting list to the subscriber list due to an unsubscription of a different member.
   **Note:** this notification is also sent when the other user is unsubscribed from the event in the backend!

5. **Event notification**

   This notification type is used when a backend user manually clicks the "Send a new event notification" button next
   to an event in the backend list module. It can mostly be used as a message to your users encouraging to subscribe
   to a new event which you have just created. The notification will address all selected members that have NOT
   subscribed to the event yet!
   

## Custom notifications per calendar
   
As of version 2.8.0 it is possible to set custom notifications per each calendar. In order to do that, simply enable
the subscriptions in the calendar settings and choose the appropriate notifications in the corresponding fields 
(e.g. `Subscribe notification`). If no subscription is chosen, then all subscriptions of a certain type will be 
sent out (standard behavior)!


## Custom notifications per event

To send other notifications for a single event, e.g. a different information pack, check "Custom notifications" in the
event settings and choose its subscribe, unsubscribe, waiting list promotion and reminder notifications. Fields left
empty use the calendar's notification. This is independent of "Override subscription settings". Reminders are still
only sent for calendars that have reminders enabled.

## Available tokens

The list of available tokens in every notification:

1. `admin_email`

   The e-mail address of administrator.

2. `recipient_email`

   The e-mail address of the subscribed user.

3. `subscription_*`

   All data of the subscribed user. Enter the field name in place of asterisk to get
   the desired data, e.g. `subscription_firstname` to get the member's firstname.

4. `subscription_waitingList`

   True if the subscriber is on the waiting list, false otherwise.

5. `event_*`

   All data of the event. Enter the field name in place of asterisk to get
   the desired data, e.g. `event_startDate` to get the event's start date.

6. `calendar_*`

   All data of the event's calendar. Enter the field name in place of asterisk to get
   the desired data, e.g. `calendar_title` to get the calendar's title.

7. `unsubscribe_link`

   A unique link that allows the user to immediately unsubscribe from the event.

8. `subscription_field_*`

   The answer to a [registration field](03-advanced.md#registration-fields) of a guest subscription. Enter the
   field key in place of asterisk, e.g. `subscription_field_job_title`. Checkboxes are rendered as yes/no.
   
8. `event_link`

   A link to the event itself.

   `event_ics_link` is a link to the event's "Add to calendar" (.ics) file.

9. `days_before_event`

   A number of days before the event, `0` if the event is past.

## E-mails (simple editor)

Events › Settings › E-mail templates shows the event notifications as a plain list: the name, when the e-mail is
sent, how many calendars, event groups and events use it, its subject, and a warning when an e-mail is not set up
(nothing is sent) or switched off. It is shown to users who may access the Notification Center.

Editing an e-mail shows one form: name, when it is sent, subject, format (plain or formatted text) and the message,
with the sender and an on/off switch under "More options". Click one of the available tags to insert it where the
cursor is; only the tags the e-mail type provides are listed, including the registration fields. "Send a test"
asks for an address (your own by default) and sends the e-mail filled with the details of the latest booking to
that address only: the e-mail's own recipients, CC and BCC are left out of the test.

The form edits the main language of the e-mail's first message in the Notification Center and creates them if they
are missing, so sending is unchanged. Use the Notification Center for anything else (more languages, CC/BCC,
attachments).

### All booking details

`##subscription_details##` ("All booking details" in the tag list) lists every answer of the booking form as
"Label: value" lines: first name, last name, e-mail address, the number of participants if the event asks for it, and
every registration field of the event. Empty answers are left out. Registration fields added later appear without
changing the e-mail. Meant for plain text e-mails.

### Trainer pack and after-the-event e-mails

Two more e-mail types are sent by the site on its own once an e-mail of that type is chosen under Events › Settings ›
Booking settings › Before and after events. Each is sent once for every event or booking, from the hourly cron job.

Both e-mails are created by `contao:migrate` with the standard e-mail fields (sender from Booking settings or from an
existing event e-mail, recipient `##recipient_email##`, subject, plain text, signature) as soon as the Notification
Center has an e-mail gateway. An e-mail that already exists is never created again or changed.

- **Trainer pack the day before** (`events_subscriptions_trainer`): goes to the trainer of the event (chosen from
  Trainers, or an e-mail address typed on it), or of its course when the event has none, at the chosen hour (16:00 by default) for events starting tomorrow. An
  event that is still today and was missed is sent at once. Tags: `##attendee_list##` (everyone booked, waiting
  list left out; each person is listed with the same lines as the `##subscription_details##` tag, so the form fields
  never have to be listed again), `##attendee_count##`, `##attendee_places##`, `##event_trainer_name##` and the event tags.
- **After the event** (`events_subscriptions_followUp`): goes the day after, from 09:00, to everyone marked as attended
  (to every confirmed booking when Attendance is switched off). Only events that ended in the last 3 days are
  considered, so switching it on never writes to people who attended long ago. Tags: the booking tags and
  `##certificate_link##` and `##feedback_link##`.

### Links to the booker's own pages

`##booking_link##` (manage my booking), `##certificate_link##` and `##feedback_link##` are available in every booking
e-mail. See "The booker's own pages" in the back end and booking chapters.

### Venue tags

With Venues and locations switched on, `##event_venue_directions##`, `##event_venue_accessibility##` and
`##event_venue_map##` give the directions, accessibility information and map link of the venue chosen on the event
(empty when there is none). The venue's name and address are the usual `##event_location##` and `##event_address##`.
