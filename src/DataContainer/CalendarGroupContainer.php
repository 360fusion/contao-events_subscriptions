<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Contao\Database;
use Contao\DataContainer;
use Contao\StringUtil;

class CalendarGroupContainer
{
    /**
     * Get all calendars, showing the group they belong to now
     */
    public function getCalendarOptions(): array
    {
        $options = [];
        $calendars = Database::getInstance()->query("SELECT c.id, c.title, g.title AS groupTitle FROM tl_calendar c LEFT JOIN tl_calendar_group g ON g.id=c.calendarGroup ORDER BY c.title");

        while ($calendars->next()) {
            $title = '' !== (string) $calendars->title ? $calendars->title : sprintf($GLOBALS['TL_LANG']['tl_calendar_group']['untitledCalendar'], $calendars->id);
            $options[$calendars->id] = $calendars->groupTitle ? sprintf('%s <span class="tl_gray">(%s)</span>', $title, $calendars->groupTitle) : $title;
        }

        return $options;
    }

    public function loadCalendars($value, DataContainer $dc): array
    {
        return Database::getInstance()->prepare('SELECT id FROM tl_calendar WHERE calendarGroup=?')->execute($dc->id)->fetchEach('id');
    }

    /**
     * Assign the ticked calendars to this group (moving them out of their previous group) and release the others
     */
    public function saveCalendars($value, DataContainer $dc): string
    {
        $ids = array_map('intval', StringUtil::deserialize($value, true));
        $db = Database::getInstance();

        $db->prepare('UPDATE tl_calendar SET calendarGroup=0 WHERE calendarGroup=?'.($ids ? ' AND id NOT IN ('.implode(',', $ids).')' : ''))->execute($dc->id);

        if ($ids) {
            $db->prepare('UPDATE tl_calendar SET calendarGroup=? WHERE id IN ('.implode(',', $ids).')')->execute($dc->id);
        }

        return '';
    }

    public function releaseCalendars(DataContainer $dc): void
    {
        Database::getInstance()->prepare('UPDATE tl_calendar SET calendarGroup=0 WHERE calendarGroup=?')->execute($dc->id);
    }

    public function generateLabel(array $row): string
    {
        $count = (int) Database::getInstance()->prepare('SELECT COUNT(*) AS total FROM tl_calendar WHERE calendarGroup=?')->execute($row['id'])->total;

        return sprintf('%s <span class="label-info">[%s]</span>', $row['title'], sprintf($GLOBALS['TL_LANG']['tl_calendar_group']['calendarCount'], $count));
    }
}
