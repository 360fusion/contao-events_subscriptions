<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Venue;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;

class VenueContainer
{
    /**
     * The venues to choose from on an event: the published ones and the one the event already has
     */
    public function getOptions(DataContainer|null $dc = null): array
    {
        $current = (int) ($dc?->activeRecord?->venue ?? 0);
        $options = [];

        foreach (Database::getInstance()->prepare('SELECT id, title, town, published FROM tl_calendar_events_venue WHERE published=1 OR id=? ORDER BY title')->execute($current)->fetchAllAssoc() as $row) {
            $options[$row['id']] = $row['title'].($row['town'] ? ' ('.$row['town'].')' : '').($row['published'] ? '' : ' ('.$GLOBALS['TL_LANG']['tl_calendar_events_venue']['unpublished'].')');
        }

        return $options;
    }

    /**
     * After an event is saved: the location and address come from its venue
     */
    public function syncEvent(DataContainer $dc): void
    {
        if (!Features::venues() || !$dc->id) {
            return;
        }

        if ($venue = (int) Database::getInstance()->prepare('SELECT venue FROM tl_calendar_events WHERE id=?')->execute($dc->id)->venue) {
            Venue::syncEvents($venue, (int) $dc->id);
        }
    }

    /**
     * An event with a venue shows its location and address as they come from the venue, not as fields to type in
     */
    public function lockLocation(DataContainer $dc): void
    {
        if (!Features::venues() || 'edit' !== Input::get('act') || !$dc->id) {
            return;
        }

        if (Database::getInstance()->prepare('SELECT venue FROM tl_calendar_events WHERE id=?')->execute($dc->id)->venue) {
            $GLOBALS['TL_DCA']['tl_calendar_events']['fields']['location']['eval']['readonly'] = true;
            $GLOBALS['TL_DCA']['tl_calendar_events']['fields']['address']['eval']['readonly'] = true;
        }
    }

    /**
     * After a venue is saved: its events show the new name and address
     */
    public function onSave(DataContainer $dc): void
    {
        if ($dc->id) {
            Venue::syncEvents((int) $dc->id);
        }
    }

    /**
     * A deleted venue leaves its events with the location and address they have now
     */
    public function onDelete(DataContainer $dc): void
    {
        Database::getInstance()->prepare('UPDATE tl_calendar_events SET venue=0 WHERE venue=?')->execute($dc->id);
    }

    public function generateLabel(array $row): string
    {
        $L = $GLOBALS['TL_LANG']['tl_calendar_events_venue'];
        $events = (int) Database::getInstance()->prepare('SELECT COUNT(*) AS n FROM tl_calendar_events WHERE venue=?')->execute($row['id'])->n;

        return sprintf(
            '%s%s<br><span class="tl_gray">%s</span>',
            StringUtil::specialchars($row['title']),
            $row['published'] ? '' : ' <span class="label-info">'.$L['unpublished'].'</span>',
            StringUtil::specialchars(Venue::address($row) ?: '-').' · '.(1 === $events ? $L['eventOne'] : sprintf($L['eventCount'], $events)),
        );
    }
}
