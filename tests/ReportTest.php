<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\Report;
use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    private function event(array $values): array
    {
        return $values + ['month' => '2026-10', 'course' => 'BLS', 'calendar' => 'Training', 'capacity' => 10, 'booked' => 0, 'attended' => 0, 'recorded' => false, 'cancelled' => 0, 'ratingSum' => 0, 'ratingCount' => 0];
    }

    public function testPlacesOverTheLimitAreTheWaitingListAndDoNotFillTheEvent(): void
    {
        $rows = Report::aggregate([$this->event(['booked' => 14])]);

        $this->assertSame(10, $rows[0]['booked']);
        $this->assertSame(4, $rows[0]['waiting']);
        $this->assertSame(100, $rows[0]['fill']);
    }

    public function testEventsWithoutALimitAreLeftOutOfTheFillRate(): void
    {
        $rows = Report::aggregate([$this->event(['capacity' => 10, 'booked' => 5]), $this->event(['capacity' => 0, 'booked' => 30])]);

        $this->assertSame(35, $rows[0]['booked']);
        $this->assertSame(50, $rows[0]['fill']); // 5 of 10, not 35 of 10
        $this->assertSame(2, $rows[0]['events']);
    }

    public function testDidNotAttendOnlyCountsEventsWhereAttendanceWasRecorded(): void
    {
        $rows = Report::aggregate([
            $this->event(['booked' => 8, 'attended' => 6, 'recorded' => true]),
            $this->event(['booked' => 9, 'attended' => 0, 'recorded' => false]), // nobody ticked: not a no-show event
        ]);

        $this->assertSame(2, $rows[0]['missed']);
        $this->assertSame(25, $rows[0]['missedRate']); // 2 of the 8 on the event that was recorded
        $this->assertSame(6, $rows[0]['attended']);
    }

    public function testNoRecordedAttendanceMeansNoRate(): void
    {
        $rows = Report::aggregate([$this->event(['booked' => 8])]);

        $this->assertNull($rows[0]['missedRate']);
        $this->assertNull($rows[0]['rating']);
    }

    public function testRowsPerMonthAndCourseNewestMonthFirstAndTheTotals(): void
    {
        $events = [
            $this->event(['month' => '2026-09', 'booked' => 4, 'cancelled' => 1, 'ratingSum' => 8, 'ratingCount' => 2]),
            $this->event(['month' => '2026-10', 'booked' => 6, 'ratingSum' => 5, 'ratingCount' => 1]),
            $this->event(['month' => '2026-10', 'booked' => 2]),
        ];
        $rows = Report::aggregate($events);

        $this->assertSame(['2026-10', '2026-09'], array_column($rows, 'month'));
        $this->assertSame(8, $rows[0]['booked']);

        $totals = Report::totals($events);
        $this->assertSame(12, $totals['booked']);
        $this->assertSame(1, $totals['cancelled']);
        $this->assertSame(3, $totals['events']);
        $this->assertSame(4.3, $totals['rating']); // 13 stars over 3 answers
    }

    public function testTotalsOfNothing(): void
    {
        $this->assertSame(0, Report::totals([])['events']);
    }
}
