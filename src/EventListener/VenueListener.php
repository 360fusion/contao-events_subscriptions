<?php

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Venue;
use Contao\Template;

class VenueListener
{
    /**
     * Put the venue information under the location of the event page. The template stays as it is (the site's own, or Contao's event_full).
     */
    public function onParseFrontendTemplate(string $buffer, string $name, Template $template): string
    {
        if (!Features::venues() || !str_starts_with($name, 'event_full') || !($id = (int) $template->venue) || null === ($venue = Venue::find($id))) {
            return $buffer;
        }

        if ('' === ($info = Venue::info($venue))) {
            return $buffer;
        }

        return preg_replace_callback('#<p class="location">.*?</p>#s', static fn (array $match) => $match[0].$info, $buffer, 1) ?? $buffer;
    }
}
