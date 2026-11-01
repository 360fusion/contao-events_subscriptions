<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\EventGroupFilter;
use PHPUnit\Framework\TestCase;

class EventGroupFilterTest extends TestCase
{
    private const EVENTS = [
        '20261105' => [
            1762333200 => [
                ['id' => 1, 'pid' => 10, 'eventGroup' => 100],
                ['id' => 2, 'pid' => 10, 'eventGroup' => 0],
            ],
        ],
        '20261201' => [
            1764579600 => [
                ['id' => 3, 'pid' => 20, 'eventGroup' => 200],
            ],
        ],
    ];

    public function testNoFilterKeepsEverything(): void
    {
        $this->assertSame(self::EVENTS, EventGroupFilter::filter(self::EVENTS, null, null, null));
    }

    public function testCalendarFilterDropsOtherCalendarsAndEmptyDays(): void
    {
        $this->assertSame([1, 2], $this->ids(EventGroupFilter::filter(self::EVENTS, 10, null, null)));
        $this->assertArrayNotHasKey('20261201', EventGroupFilter::filter(self::EVENTS, 10, null, null));
    }

    public function testGroupFilter(): void
    {
        $this->assertSame([1], $this->ids(EventGroupFilter::filter(self::EVENTS, null, 100, null)));
        $this->assertSame([1], $this->ids(EventGroupFilter::filter(self::EVENTS, 10, 100, null)));
        $this->assertSame([], $this->ids(EventGroupFilter::filter(self::EVENTS, 20, 100, null)));
    }

    public function testUnknownIdsMatchNothing(): void
    {
        $this->assertSame([], EventGroupFilter::filter(self::EVENTS, EventGroupFilter::UNKNOWN, null, null));
        $this->assertSame([], EventGroupFilter::filter(self::EVENTS, null, EventGroupFilter::UNKNOWN, null));
    }

    public function testResolve(): void
    {
        $ids = ['palliative-care' => 10, '20' => 20];

        $this->assertNull(EventGroupFilter::resolve(null, $ids));
        $this->assertNull(EventGroupFilter::resolve('', $ids));
        $this->assertSame(10, EventGroupFilter::resolve('palliative-care', $ids));
        $this->assertSame(10, EventGroupFilter::resolve('10', $ids));
        $this->assertSame(20, EventGroupFilter::resolve('20', $ids));
        $this->assertSame(EventGroupFilter::UNKNOWN, EventGroupFilter::resolve('nope', $ids));
        $this->assertSame(EventGroupFilter::UNKNOWN, EventGroupFilter::resolve('99', $ids));
    }

    public function testMonthFilter(): void
    {
        $this->assertSame([3], $this->ids(EventGroupFilter::filter(self::EVENTS, null, null, '202612')));
    }

    private function ids(array $allEvents): array
    {
        $ids = [];

        array_walk_recursive($allEvents, static function ($value, $key) use (&$ids) {
            if ('id' === $key) {
                $ids[] = $value;
            }
        });

        return $ids;
    }
}
