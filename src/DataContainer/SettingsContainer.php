<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Contao\Config;
use Contao\Database;

class SettingsContainer
{
    /**
     * Calendars with subscriptions, to copy the booking settings of
     */
    public function getCalendarOptions(): array
    {
        $result = Database::getInstance()->query('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title');

        return array_combine($result->fetchEach('id'), $result->fetchEach('title')) ?: [];
    }

    /**
     * A new calendar starts with the booking settings of the template calendar (Events › Setup › Booking settings)
     */
    public function applyTemplateCalendar(string $table, int $insertId): void
    {
        $templateId = (int) Config::get('eventsSubscriptionsTemplateCalendar');

        if (!$templateId || $templateId === $insertId) {
            return;
        }

        $template = Database::getInstance()->prepare('SELECT * FROM tl_calendar WHERE id=?')->execute($templateId)->fetchAssoc();

        if (!$template) {
            return;
        }

        $set = array_filter($template, static fn ($key) => str_starts_with((string) $key, 'subscription_'), ARRAY_FILTER_USE_KEY);

        if ($set) {
            Database::getInstance()->prepare('UPDATE tl_calendar %s WHERE id=?')->set($set)->execute($insertId);
        }
    }
}
