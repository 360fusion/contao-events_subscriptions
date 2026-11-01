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
- **E-mails**: the simple editor of the event e-mails
- **Payments**: payments, refunds and invoices
- **Setup**: tiles for the registration fields, the payment providers and (when switched on) the calendar groups
