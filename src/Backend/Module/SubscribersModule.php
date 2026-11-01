<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\Backend\SubscribersController;

/**
 * Events › Subscribers (all calendars)
 */
class SubscribersModule
{
    public function __construct(mixed $dc = null)
    {
    }

    public function generate(): string
    {
        return (string) (new SubscribersController())->run();
    }
}
