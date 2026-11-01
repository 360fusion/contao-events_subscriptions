<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\Trainer;
use Contao\Database;
use Contao\DataContainer;
use Contao\StringUtil;

class TrainerContainer
{
    /**
     * The trainers to choose from on an event or course: the available ones and the one already chosen
     */
    public function getOptions(DataContainer|null $dc = null): array
    {
        $current = (int) ($dc?->activeRecord?->subscription_trainer ?? 0);
        $options = [];

        foreach (Database::getInstance()->prepare('SELECT id, name, published FROM tl_calendar_events_trainer WHERE published=1 OR id=? ORDER BY name')->execute($current)->fetchAllAssoc() as $row) {
            $options[$row['id']] = $row['name'].($row['published'] ? '' : ' ('.$GLOBALS['TL_LANG']['tl_calendar_events_trainer']['unpublished'].')');
        }

        return $options;
    }

    /**
     * A deleted trainer leaves the events and courses that used them with their e-mail address, so the trainer pack still goes out
     */
    public function onDelete(DataContainer $dc): void
    {
        $db = Database::getInstance();

        if (null !== ($trainer = Trainer::find((int) $dc->id)) && '' !== trim((string) $trainer['email'])) {
            foreach (['tl_calendar_events', 'tl_calendar_event_group'] as $table) {
                $db->prepare("UPDATE $table SET subscription_trainerEmail=? WHERE subscription_trainer=? AND subscription_trainerEmail=''")->execute(trim($trainer['email']), $dc->id);
            }
        }

        foreach (['tl_calendar_events', 'tl_calendar_event_group'] as $table) {
            $db->prepare("UPDATE $table SET subscription_trainer=0 WHERE subscription_trainer=?")->execute($dc->id);
        }
    }

    public function generateLabel(array $row): string
    {
        $L = $GLOBALS['TL_LANG']['tl_calendar_events_trainer'];
        $db = Database::getInstance();

        return sprintf(
            '%s%s<br><span class="tl_gray">%s · %s</span>',
            StringUtil::specialchars($row['name']),
            $row['published'] ? '' : ' <span class="label-info">'.$L['unpublished'].'</span>',
            StringUtil::specialchars($row['email']),
            sprintf($L['usage'], (int) $db->prepare('SELECT COUNT(*) AS n FROM tl_calendar_events WHERE subscription_trainer=?')->execute($row['id'])->n, (int) $db->prepare('SELECT COUNT(*) AS n FROM tl_calendar_event_group WHERE subscription_trainer=?')->execute($row['id'])->n),
        );
    }
}
