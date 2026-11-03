<?php

namespace Codefog\EventsSubscriptionsBundle;

/**
 * Who moves up from the waiting list when a booking goes away. Bookings are taken in the order they were made; a booking
 * waits when the places of everyone before it and its own go over the maximum.
 */
final class WaitingList
{
    /**
     * @param list<array{id: int, places: int}> $bookings Every booking of the event, in the order they were made
     *
     * @return list<int> The IDs that wait with the booking and do not any more without it, in order
     */
    public static function promoted(array $bookings, int $cancelledId, int $max): array
    {
        $before = $after = 0;
        $promoted = [];

        foreach ($bookings as $booking) {
            $waitedBefore = $before + $booking['places'] > $max;
            $before += $booking['places'];

            if ($booking['id'] === $cancelledId) {
                continue;
            }

            $waitsNow = $after + $booking['places'] > $max;
            $after += $booking['places'];

            if ($waitedBefore && !$waitsNow) {
                $promoted[] = $booking['id'];
            }
        }

        return $promoted;
    }
}
