# Going live

A checklist for moving a site from testing to the real thing. Do each step on a **copy** of the live site first (restore the
live database backup into a second database and point a test copy of the site at it).

## 1. Before you touch anything

- [ ] Back up the live database (`mysqldump`) and the `files/` and `templates/` folders. Keep the backup until the new
  version has run for a week.
- [ ] Write down the version you run now, so you can go back (`composer show codefog/contao-events_subscriptions`).
- [ ] Make sure the site has a working e-mail setup (`MAILER_DSN`) and a real cron job (see step 4).

## 2. Install the update on the copy

- [ ] Add the repository and require the version (see the README), then `composer update codefog/contao-events_subscriptions`.
- [ ] Run `contao:migrate --dry-run` and read every line. The update adds tables, columns and the ready-made e-mails.
  Never add `--with-deletes` without knowing what it will drop.
- [ ] Run `contao:migrate`, clear the cache, and open Events › Settings: every tile must open.
- [ ] Work through [the testing checklist](09-testing-checklist.md) on the copy.

## 3. Settings to decide on purpose

- [ ] Events › Settings › Booking settings: switch features on only when you want them. Nothing is created for you.
- [ ] Payments: leave **off** until the provider's test mode has worked (see step 5).
- [ ] E-mail templates: read every ready-made e-mail, change the wording and the signature. A calendar that chooses no
  e-mail sends every e-mail of that type.
- [ ] If the booking form is public: *Bookings per IP address in 10 minutes* in General, and consider *Confirm the e-mail
  address of free e-mail domains* in Payments.
- [ ] Set `framework.trusted_hosts` (or give the root page a domain) so links in e-mails use your domain.

## 4. Scheduled jobs

Reminders, trainer packs, thank-you e-mails, expiring unpaid or unconfirmed bookings and the removal of old booking data
all run from Contao's cron. Use a real cron job, every minute:

```
* * * * * php /path/to/site/vendor/bin/contao-console contao:cron
```

Without it Contao runs the jobs when someone visits the site, which is too slow for the one-minute jobs.

## 5. Payments

- [ ] Create the provider account yourself and enter the **test** keys in Events › Payments. Check that the webhook address
  shown there can be reached from the internet (the provider calls it).
- [ ] Pay with the provider's test cards, refund one, abandon one, and see the hold time free the place.
- [ ] Only then enter the live keys and switch the provider to live mode.

## 6. After going live

- [ ] Book once yourself with a real address and check the e-mails.
- [ ] Watch System › System log for lines that mention "paid after the booking expired" (a payment arrived after the hold
  ran out: refund it or book the person in) and for failed e-mails.
- [ ] Keep Events › Settings › Health check open for a week.

## If something goes wrong

Restore the database backup, put the files back, and `composer require` the previous version.
