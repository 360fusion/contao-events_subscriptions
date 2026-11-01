<?php

namespace Codefog\EventsSubscriptionsBundle;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Contao\Database;

/**
 * Numbers for Events › Settings › Reports. Unpaid card bookings (pending, failed) are never counted.
 * "Places" are numberOfParticipants, as everywhere else; a booking without the field counts as one place.
 */
final class Report
{
    private const BOOKED = "s.paymentStatus NOT IN ('pending', 'failed')";

    /**
     * The events of the period with their sums, one entry for each event
     *
     * @return list<array{month: string, course: string, calendar: string, capacity: int, booked: int, attended: int, recorded: bool, cancelled: int, ratingSum: int, ratingCount: int}>
     */
    public static function events(int $from, int $to, int $calendar = 0): array
    {
        $db = Database::getInstance();
        $where = 'c.subscription_enable=1 AND e.published=1 AND e.startTime BETWEEN ? AND ?'.($calendar ? ' AND e.pid=?' : '');
        $values = $calendar ? [$from, $to, $calendar] : [$from, $to];
        $join = 'JOIN tl_calendar_events e ON e.id=%1$s.pid JOIN tl_calendar c ON c.id=e.pid WHERE '.$where;

        $events = $db->prepare("SELECT e.id, e.startTime, e.title, e.pid, (SELECT title FROM tl_calendar_event_group WHERE id=e.eventGroup) AS course, c.title AS calendar FROM tl_calendar_events e JOIN tl_calendar c ON c.id=e.pid WHERE $where ORDER BY e.startTime")
            ->execute(...$values)->fetchAllAssoc();

        $bookings = self::byPid($db->prepare('SELECT s.pid, SUM(GREATEST(s.numberOfParticipants, 1)) AS booked, SUM(IF(s.attended, GREATEST(s.numberOfParticipants, 1), 0)) AS attended, MAX(s.attended) AS recorded FROM tl_calendar_events_subscription s '.sprintf($join, 's').' AND '.self::BOOKED.' GROUP BY s.pid')->execute(...$values));
        $cancellations = self::byPid($db->prepare('SELECT x.pid, SUM(x.places) AS cancelled FROM tl_calendar_events_cancellation x '.sprintf($join, 'x').' GROUP BY x.pid')->execute(...$values));
        $feedback = self::byPid($db->prepare('SELECT f.pid, COUNT(*) AS ratingCount, SUM(f.rating) AS ratingSum FROM tl_calendar_events_feedback f '.sprintf($join, 'f').' GROUP BY f.pid')->execute(...$values));
        $factory = Services::getEventConfigFactory();
        $result = [];

        foreach ($events as $event) {
            try {
                $capacity = (int) $factory->create((int) $event['id'])->getMaximumSubscriptions();
            } catch (\Throwable) {
                $capacity = 0;
            }

            $id = $event['id'];
            $result[] = [
                'month' => date('Y-m', (int) $event['startTime']),
                'course' => (string) ($event['course'] ?: $event['title']),
                'calendar' => (string) $event['calendar'],
                'capacity' => $capacity,
                'booked' => (int) ($bookings[$id]['booked'] ?? 0),
                'attended' => (int) ($bookings[$id]['attended'] ?? 0),
                'recorded' => (bool) ($bookings[$id]['recorded'] ?? false),
                'cancelled' => (int) ($cancellations[$id]['cancelled'] ?? 0),
                'ratingSum' => (int) ($feedback[$id]['ratingSum'] ?? 0),
                'ratingCount' => (int) ($feedback[$id]['ratingCount'] ?? 0),
            ];
        }

        return $result;
    }

    /**
     * One row for each month and course, newest month first.
     * - Places over the limit are the waiting list, not bookings.
     * - The fill rate only counts events with a limit.
     * - Did-not-attend only counts events where attendance was recorded (someone is marked as attended).
     *
     * @param list<array> $events As returned by events()
     *
     * @return list<array{month: string, course: string, calendar: string, events: int, capacity: int, booked: int, waiting: int, fill: int|null, attended: int, missed: int, missedRate: int|null, cancelled: int, rating: float|null, ratingCount: int}>
     */
    public static function aggregate(array $events): array
    {
        $rows = [];

        foreach ($events as $event) {
            $key = $event['month']."\0".$event['calendar']."\0".$event['course'];
            $row = &$rows[$key];
            $row ??= ['month' => $event['month'], 'course' => $event['course'], 'calendar' => $event['calendar'], 'events' => 0, 'capacity' => 0, 'booked' => 0, 'waiting' => 0, 'limitedBooked' => 0, 'attended' => 0, 'recordedBooked' => 0, 'cancelled' => 0, 'ratingSum' => 0, 'ratingCount' => 0];

            $limited = $event['capacity'] > 0;
            $confirmed = $limited ? min($event['booked'], $event['capacity']) : $event['booked'];

            ++$row['events'];
            $row['booked'] += $confirmed;
            $row['waiting'] += $limited ? max(0, $event['booked'] - $event['capacity']) : 0;
            $row['cancelled'] += $event['cancelled'];
            $row['ratingSum'] += $event['ratingSum'];
            $row['ratingCount'] += $event['ratingCount'];

            if ($limited) {
                $row['capacity'] += $event['capacity'];
                $row['limitedBooked'] += $confirmed;
            }

            if ($event['recorded']) {
                $row['attended'] += min($event['attended'], $confirmed);
                $row['recordedBooked'] += $confirmed;
            }

            unset($row);
        }

        uasort($rows, static fn (array $a, array $b) => [$b['month'], $a['calendar'], $a['course']] <=> [$a['month'], $b['calendar'], $b['course']]);

        return array_values(array_map(static fn (array $row) => self::finish($row), $rows));
    }

    /**
     * The sums of all rows, in the same shape
     */
    public static function totals(array $events): array
    {
        $rows = self::aggregate(array_map(static fn (array $event) => ['month' => '', 'course' => '', 'calendar' => ''] + $event, $events));

        return $rows[0] ?? self::finish(['month' => '', 'course' => '', 'calendar' => '', 'events' => 0, 'capacity' => 0, 'booked' => 0, 'waiting' => 0, 'limitedBooked' => 0, 'attended' => 0, 'recordedBooked' => 0, 'cancelled' => 0, 'ratingSum' => 0, 'ratingCount' => 0]);
    }

    /**
     * People who booked and did not attend in the last 12 months, by e-mail address (lower case).
     * Only events where attendance was recorded count, and waiting list places never do.
     *
     * @param list<string>|null $emails Only these addresses, or everyone
     *
     * @return array<string, array{missed: int, last: int, name: string, booking: int}>
     */
    public static function missedCounts(array|null $emails = null): array
    {
        if ([] === $emails) {
            return [];
        }

        $db = Database::getInstance();
        $email = "LOWER(IF(s.type='member', m.email, s.email))";
        $where = ["$email!=''", 's.attended=0', 'GREATEST(e.startTime, e.endTime)<UNIX_TIMESTAMP()', 'e.startTime>=?', self::BOOKED, 'EXISTS (SELECT 1 FROM tl_calendar_events_subscription a WHERE a.pid=e.id AND a.attended=1)'];
        $values = [strtotime('-12 months')];

        if (null !== $emails) {
            $where[] = "$email IN (".implode(',', array_fill(0, \count($emails), '?')).')';
            array_push($values, ...array_map(static fn (string $value) => strtolower(trim($value)), $emails));
        }

        $candidates = $db->prepare("SELECT s.id, s.pid, s.dateCreated, $email AS email, IF(s.type='member', CONCAT_WS(' ', m.firstname, m.lastname), CONCAT_WS(' ', s.firstname, s.lastname)) AS name, e.startTime FROM tl_calendar_events_subscription s JOIN tl_calendar_events e ON e.id=s.pid LEFT JOIN tl_member m ON m.id=s.member WHERE ".implode(' AND ', $where).' ORDER BY e.startTime')
            ->execute(...$values)->fetchAllAssoc();

        $waiting = self::waitingIds(array_unique(array_column($candidates, 'pid')));
        $people = [];

        foreach ($candidates as $row) {
            if (isset($waiting[$row['id']])) {
                continue;
            }

            $person = &$people[$row['email']];
            $person ??= ['missed' => 0, 'last' => 0, 'name' => '', 'booking' => 0];
            ++$person['missed'];
            $person['last'] = max($person['last'], (int) $row['startTime']);
            $person['name'] = $row['name'];
            $person['booking'] = max($person['booking'], (int) $row['id']);
            unset($person);
        }

        return $people;
    }

    /**
     * Every booking of the person behind a booking, with the outcome
     *
     * @return array{name: string, email: string, rows: list<array>}|null
     */
    public static function person(int $bookingId): array|null
    {
        $db = Database::getInstance();
        $row = $db->prepare("SELECT s.type, s.member, s.firstname, s.lastname, s.email FROM tl_calendar_events_subscription s WHERE s.id=?")->execute($bookingId);

        if (!$row->numRows) {
            return null;
        }

        $member = 'member' === $row->type && $row->member ? $db->prepare('SELECT firstname, lastname, email FROM tl_member WHERE id=?')->execute($row->member) : null;
        $name = trim(($member ?? $row)->firstname.' '.($member ?? $row)->lastname);
        $email = strtolower(trim((string) ($member ?? $row)->email));

        if ('' === $email) {
            return ['name' => $name, 'email' => '', 'rows' => []];
        }

        $bookings = $db->prepare("SELECT s.id, s.attended, s.paymentStatus, s.dateCreated, e.pid, e.title, e.startTime, e.endTime, e.addTime, (SELECT title FROM tl_calendar_event_group WHERE id=e.eventGroup) AS course, (SELECT title FROM tl_calendar WHERE id=e.pid) AS calendar, (SELECT COUNT(*) FROM tl_calendar_events_subscription a WHERE a.pid=e.id AND a.attended=1) AS recorded FROM tl_calendar_events_subscription s JOIN tl_calendar_events e ON e.id=s.pid LEFT JOIN tl_member m ON m.id=s.member WHERE LOWER(IF(s.type='member', m.email, s.email))=? ORDER BY e.startTime DESC")
            ->execute($email)->fetchAllAssoc();
        $factory = Services::getSubscriptionFactory();

        foreach ($bookings as &$booking) {
            try {
                $booking['waiting'] = (bool) $factory->createFromModel(SubscriptionModel::findByPk($booking['id']))->isOnWaitingList();
            } catch (\Throwable) {
                $booking['waiting'] = false;
            }

            $ended = max((int) $booking['startTime'], (int) $booking['endTime']) < time();
            $booking['outcome'] = match (true) {
                $booking['waiting'] => 'waiting',
                (bool) $booking['attended'] => 'attended',
                $ended && $booking['recorded'] > 0 => 'missed',
                $ended => 'notRecorded',
                default => 'upcoming',
            };
        }

        return ['name' => $name, 'email' => $email, 'rows' => $bookings];
    }

    /**
     * Bookings that are on a waiting list: the places over the limit, in booking order (as the booking form decides it)
     *
     * @param list<int|string> $eventIds
     *
     * @return array<int, true> Booking IDs
     */
    private static function waitingIds(array $eventIds): array
    {
        $ids = [];
        $factory = Services::getEventConfigFactory();

        foreach ($eventIds as $eventId) {
            try {
                $capacity = (int) $factory->create((int) $eventId)->getMaximumSubscriptions();
            } catch (\Throwable) {
                continue;
            }

            if ($capacity < 1) {
                continue;
            }

            $taken = 0;

            foreach (Database::getInstance()->prepare('SELECT id, GREATEST(numberOfParticipants, 1) AS places FROM tl_calendar_events_subscription WHERE pid=? ORDER BY dateCreated, id')->execute($eventId)->fetchAllAssoc() as $booking) {
                $taken += $booking['places'];

                if ($taken > $capacity) {
                    $ids[(int) $booking['id']] = true;
                }
            }
        }

        return $ids;
    }

    private static function finish(array $row): array
    {
        $row['fill'] = $row['capacity'] > 0 ? (int) round($row['limitedBooked'] / $row['capacity'] * 100) : null;
        $row['missed'] = max(0, $row['recordedBooked'] - $row['attended']);
        $row['missedRate'] = $row['recordedBooked'] > 0 ? (int) round($row['missed'] / $row['recordedBooked'] * 100) : null;
        $row['rating'] = $row['ratingCount'] > 0 ? round($row['ratingSum'] / $row['ratingCount'], 1) : null;
        unset($row['limitedBooked'], $row['recordedBooked'], $row['ratingSum']);

        return $row;
    }

    /**
     * @return array<int, array>
     */
    private static function byPid(\Contao\Database\Result $result): array
    {
        $rows = [];

        foreach ($result->fetchAllAssoc() as $row) {
            $rows[(int) $row['pid']] = $row;
        }

        return $rows;
    }
}
