# Frontend modules – Events Subscriptions

1. [Installation](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [**Frontend modules**](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Event list subscribe

Based on the genuine event list module enhanced with the subscription functionality. Since it is based 
on the genuine event list module it offers the same features as it's ancestor.


## Event reader subscribe

Based on the genuine event reader module enhanced with the subscription functionality. Since it is based 
on the genuine event reader module it offers the same features as it's ancestor.


### All calendars and filter by URL

Both modules have an "All calendars" option: they then show the events of every calendar, including calendars added
later, so the module never needs editing. The event list (and Contao's own event list and event menu) have a
"Filter by URL" option: they then only show the events matching `?calendar=<alias>`, `?group=<alias>` and
`?month=YYYYMM` in the URL. An alias that does not exist shows no events.


## Event navigation

Lists the calendars and their event groups, with the number of upcoming events, linking to the redirect page
(or the current page) with `?calendar=` or `?group=`. Place it next to an event list with "Filter by URL". Choose
the `mod_event_navigation_filter` template to show it as a form with category, event group and month dropdowns
instead of a menu. Calendars and groups without upcoming events can be hidden.


## Subscribe event form

The module that should be placed on the same page as the event reader module. It displays
a subscription form for the current event. 


## User event subscriptions

This module will display the events that the user is subscribed to. Works great as a subscription
overview/history for the currently logged in user. Since it is based on the genuine event list module
it offers the same features as it's ancestor.


## CSS classes

Since version 2.8.0 all the events that member is subscribed to, will get a `subscribed` CSS class in the event modules
such as event list and calendar.

Since version 2.13 the `can-subscribe` CSS class is also added to the calendar module.


## Note on `cal_` and `event_` templates

You may need to adjust some of the `cal_` and `event_` templates to enable the extra data and subscription form.
The extension comes by default with the `event_list_subscribe` template which works as an example
but should not be used (at least unmodified) in the production.

If you wonder what data is available for the template you can simply print it:

```php
<?php $this->dumpTemplateVars() ?>
```

Example data you can find is:

```php
$this->subscribers; // all subscribers
$this->subscribers['subscribers']; // event subscribers
$this->subscribers['subscribersParticipants']; // total number of participants of event subscribers
$this->subscribers['subscribersData']; // detailed subscription data of subscribers (since version 2.13)
$this->subscribers['waitingList']; // subscribers on waiting list
$this->subscribers['waitingListParticipants']; // total number of participants of subscribers on waiting list
$this->subscribers['waitingListData']; // detailed subscription data of subscribers on waiting list (since version 2.13)
$this->subscriptionMaximum; // maximum subscribers limit
$this->subscriptionTypes; // detailed information about subscription types
$this->subscriptionWaitingList; // 1 – waiting list enabled; 0 – disabled
$this->subscriptionWaitingListLimit; // waiting list limit
$this->subscribeWaitingList; // true if subscribe will be made to waiting list
```

Since version 2.13 For the calendar `cal_` templates, each event should contain the similar data:

```php
<?php foreach ($day['events'] as $event): ?>
Subscribers: <?= $event['subscribers']['subscribersParticipants'] + $event['subscribers']['waitingListParticipants'] ?> / <?= $event['subscriptionMaximum'] ?>
<?php endforeach; ?>
```
