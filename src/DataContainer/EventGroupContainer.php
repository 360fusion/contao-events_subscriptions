<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Contao\Database;
use Contao\DataContainer;
use Contao\System;

class EventGroupContainer
{
    /**
     * Generate the alias from the title if it is empty. Used by tl_calendar and tl_calendar_event_group.
     */
    public function generateAlias($value, DataContainer $dc): string
    {
        $aliasExists = static fn (string $alias): bool => Database::getInstance()
            ->prepare('SELECT id FROM '.$dc->table.' WHERE alias=? AND id!=?')
            ->execute($alias, $dc->id)
            ->numRows > 0;

        if (!$value) {
            return System::getContainer()->get('contao.slug')->generate((string) $dc->activeRecord->title, [], $aliasExists);
        }

        if (preg_match('/^[1-9]\d*$/', $value)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $value));
        }

        if ($aliasExists($value)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $value));
        }

        return $value;
    }

    /**
     * The groups of the event's calendar (or of the listed calendar, for the filter panel)
     */
    public function getEventGroupOptions(DataContainer $dc): array
    {
        $pid = $dc->activeRecord?->pid ?: $dc->currentPid;

        if (!$pid) {
            return [];
        }

        $groups = Database::getInstance()->prepare('SELECT id, title FROM tl_calendar_event_group WHERE pid=? ORDER BY sorting')->execute($pid);

        return array_combine($groups->fetchEach('id'), $groups->fetchEach('title')) ?: [];
    }

    public function releaseEvents(DataContainer $dc): void
    {
        Database::getInstance()->prepare('UPDATE tl_calendar_events SET eventGroup=0 WHERE eventGroup=?')->execute($dc->id);
    }

    public function generateLabel(array $row): string
    {
        $count = (int) Database::getInstance()->prepare('SELECT COUNT(*) AS total FROM tl_calendar_events WHERE eventGroup=?')->execute($row['id'])->total;

        return sprintf('%s <span class="label-info">[%s]</span>', $row['title'], sprintf($GLOBALS['TL_LANG']['tl_calendar_event_group']['eventCount'], $count));
    }
}
