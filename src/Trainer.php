<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\Database;

/**
 * Trainers (Events › Trainers). An event or course has a trainer chosen from the list, or an e-mail address typed in.
 */
final class Trainer
{
    public static function find(int $id): array|null
    {
        $row = Database::getInstance()->prepare('SELECT * FROM tl_calendar_events_trainer WHERE id=?')->execute($id);

        return $row->numRows ? $row->row() : null;
    }

    /**
     * Who trains an event: what is set on the event, otherwise what is set on its course. On either, a trainer from the list
     * comes before a typed address. A chosen trainer is always honoured, also after the Trainers list is switched off.
     *
     * @param array $event An event row (tl_calendar_events)
     *
     * @return array{name: string, email: string} Empty when nobody is set
     */
    public static function forEvent(array $event): array
    {
        $sources = [$event];

        if (!empty($event['eventGroup'])) {
            $group = Database::getInstance()->prepare('SELECT subscription_trainer, subscription_trainerEmail FROM tl_calendar_event_group WHERE id=?')->execute($event['eventGroup']);

            if ($group->numRows) {
                $sources[] = $group->row();
            }
        }

        foreach ($sources as $source) {
            if (($id = (int) ($source['subscription_trainer'] ?? 0)) && ($trainer = self::find($id)) && '' !== trim((string) $trainer['email'])) {
                return ['name' => trim((string) $trainer['name']), 'email' => trim((string) $trainer['email'])];
            }

            if ('' !== ($email = trim((string) ($source['subscription_trainerEmail'] ?? '')))) {
                return ['name' => '', 'email' => $email];
            }
        }

        return ['name' => '', 'email' => ''];
    }
}
