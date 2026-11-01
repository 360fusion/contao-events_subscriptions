<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\Backend\UpcomingController;

/**
 * Events › Upcoming events
 */
class UpcomingModule
{
    public function __construct(mixed $dc = null)
    {
    }

    public function generate(): string
    {
        return (string) (new UpcomingController())->run();
    }
}
