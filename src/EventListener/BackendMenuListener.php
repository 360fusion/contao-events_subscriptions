<?php

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Features;
use Contao\CoreBundle\Event\MenuEvent;

/**
 * Show the "Events" group right after "Content" in the back end menu (other extensions may insert their groups first)
 */
class BackendMenuListener
{
    public function __invoke(MenuEvent $event): void
    {
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        // Payments switched off in Events › Settings › Booking settings
        if (!Features::payments()) {
            $tree->getChild('events_subscriptions')?->removeChild('event_payments');
        }

        $names = array_keys($tree->getChildren());

        if (!\in_array('events_subscriptions', $names, true) || false === ($content = array_search('content', $names, true))) {
            return;
        }

        $names = array_values(array_diff($names, ['events_subscriptions']));
        array_splice($names, $content + 1, 0, ['events_subscriptions']);

        $tree->reorderChildren($names);
    }
}
