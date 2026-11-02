# Events Subscriptions for Contao: booking suite

A fork of [Codefog's Events Subscriptions](https://github.com/codefog/contao-events_subscriptions) for Contao 5.7, with
paid bookings, a Settings area, running-the-day tools and reports added. It keeps the original package name and
namespace, so it replaces Codefog's package on a site; the two cannot be installed together.

Events Subscriptions is an extension for the [Contao Open Source CMS](https://contao.org).

Contao extension that allows users of your website to subscribe to the events. In the backend there is an overview 
of users subscribed to each event. Front end comes up with several frontend modules. You can also setup a reminder 
for users subscribed to the events that will be sent a certain amount of days before event takes place!

The extension allows the registered members as well as anonymous users to subscribe.

![](docs/images/preview.png)

## What this fork adds

- **Events and calendars:** event groups and calendar groups, with booking defaults per group; one booking per course;
  allowed e-mail domains; unique guest e-mails.
- **Registration fields:** extra booking form questions managed in the back end, with a tag that lists every answer in e-mails.
- **Payments:** Stripe, PayPal, Mollie and GOV.UK Pay, pay by invoice or purchase order, free e-mail domains, a payments
  list with refunds and mark-as-paid, and a hold time on unpaid card checkouts.
- **Running the day:** attendance, QR check-in, sign-in sheet, certificates of attendance, trainer pack and
  after-the-event e-mails, and a "manage my booking" page for bookers.
- **Back end:** Events › Settings with a page for each area, a simple e-mail template list, subscribers and upcoming
  events lists, venues and trainers lists, a health check, and removal of old booking data.
- **Reports:** fill rate, cancellations, attendance and feedback, repeat no-shows and a person history, with CSV, Excel,
  ODS and print/PDF downloads.
- **Contao 5 fixes:** cron jobs, flash messages and the member group tool work on Contao 5.7.

## Install

Requires PHP 8.3 and Contao 5.7. In your site's `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/360fusion/contao-events_subscriptions" }
],
"require": {
    "codefog/contao-events_subscriptions": "dev-booking-suite"
}
```

Then run `composer update codefog/contao-events_subscriptions`, check what the database update will do with
`contao:migrate --dry-run`, and run `contao:migrate`.

## Documentation

1. [Installation](docs/01-installation.md)
2. [Basic configuration](docs/02-basics.md)
3. [Advanced configuration](docs/03-advanced.md)
4. [Backend interface](docs/04-backend.md)
5. [Frontend modules](docs/05-frontend-modules.md)
6. [Notifications](docs/06-notifications.md)
7. [Insert tags](docs/07-insert-tags.md)
8. [Developers](docs/08-developers.md)

## Copyright

The original extension was created by [Codefog](https://codefog.pl) (MIT licence). The additions in this fork are
by 360fusion, under the same licence.
