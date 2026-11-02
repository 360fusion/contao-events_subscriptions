# Installation – Events Subscriptions

1. [**Installation**](01-installation.md)
2. [Basic configuration](02-basics.md)
3. [Advanced configuration](03-advanced.md)
4. [Backend interface](04-backend.md)
5. [Frontend modules](05-frontend-modules.md)
6. [Notifications](06-notifications.md)
7. [Insert tags](07-insert-tags.md)
8. [Developers](08-developers.md)


## Installation

Minimum requirements:

 - PHP 8.3
 - Contao 5.7 with the Calendar bundle
 - Haste 5.2 and Notification Center 2.4 (installed by Composer)


### Install using Composer

This fork is not on Packagist. Add it as a repository in your site's `composer.json`:

    "repositories": [
        { "type": "vcs", "url": "https://github.com/360fusion/contao-events_subscriptions" }
    ]

then require the branch, or a tagged version such as `v3.3.0-360`:

    $ composer require codefog/contao-events_subscriptions:dev-booking-suite

It takes the place of Codefog's package of the same name. Run `contao:migrate --dry-run` to see what the database update
will do, then `contao:migrate`.


## Upgrading from 1.x to 2.x

Quite a few things has changed since version 1.x of the extension but most of them will
be handled automatically by the script. However the thing you must manually adjust is the
notification reminder which has to be created in the Notification Center module. For more
information on how to do it please read the documentation.



[1]: https://getcomposer.org
[3]: https://github.com/codefog/contao-events_subscriptions/archive/master.zip
[4]: https://github.com/codefog/contao-haste/archive/master.zip
[5]: https://github.com/terminal42/contao-notification_center/archive/master.zip
