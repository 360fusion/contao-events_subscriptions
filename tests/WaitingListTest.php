<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\WaitingList;
use PHPUnit\Framework\TestCase;

class WaitingListTest extends TestCase
{
    private function bookings(int ...$places): array
    {
        return array_map(static fn (int $n, int $i) => ['id' => $i + 1, 'places' => $n], $places, array_keys($places));
    }

    public function testTheFirstOnTheWaitingListMovesUp(): void
    {
        // Max 2: bookings 1 and 2 have a place, 3 and 4 wait
        $this->assertSame([3], WaitingList::promoted($this->bookings(1, 1, 1, 1), 1, 2));
        $this->assertSame([3], WaitingList::promoted($this->bookings(1, 1, 1, 1), 2, 2));
    }

    public function testSeveralMoveUpWhenSeveralPlacesAreFree(): void
    {
        // Max 4: A(2) B(2) have a place, C(1) D(1) wait. A cancels: both waiting people fit.
        $this->assertSame([3, 4], WaitingList::promoted($this->bookings(2, 2, 1, 1), 1, 4));
    }

    public function testNobodyMovesUpWhenThereWasNoWaitingList(): void
    {
        $this->assertSame([], WaitingList::promoted($this->bookings(1, 1), 1, 5));
    }

    public function testNobodyMovesUpWhenTheCancelledWaitedAtTheEnd(): void
    {
        $this->assertSame([], WaitingList::promoted($this->bookings(1, 1, 1, 1), 4, 2));
    }

    public function testALargeBookingStaysOnTheWaitingListUntilItFits(): void
    {
        // Max 3: A(2) has a place; B(2) waits (4 > 3), C(1) waits behind it (5 > 3). A(2) cancels: B fits, C fits (3 <= 3).
        $this->assertSame([2, 3], WaitingList::promoted($this->bookings(2, 2, 1), 1, 3));
        // Only one place frees up: the first of the waiting two with 1 place each moves up
        $this->assertSame([3], WaitingList::promoted($this->bookings(1, 1, 1, 1), 2, 2));
    }
}
