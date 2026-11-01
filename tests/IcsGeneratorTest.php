<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\IcsGenerator;
use PHPUnit\Framework\TestCase;

class IcsGeneratorTest extends TestCase
{
    private const URL = 'https://events.example/event-details/sepsis-study-day';

    public function testTimedEvent(): void
    {
        $ics = IcsGenerator::generate([
            'id' => 5,
            'title' => 'Sepsis Study Day &amp; Q&amp;A, part 1; room 3',
            'addTime' => 1,
            'startTime' => gmmktime(9, 0, 0, 3, 10, 2027),
            'endTime' => gmmktime(16, 30, 0, 3, 10, 2027),
            'location' => 'STRIVE, JCUH',
            'address' => '',
            'teaser' => '<p>Bring your badge.</p>',
        ], self::URL, gmmktime(12, 0, 0, 11, 1, 2026));

        $this->assertStringStartsWith("BEGIN:VCALENDAR\r\n", $ics);
        $this->assertStringContainsString("UID:event-5@events.example\r\n", $ics);
        $this->assertStringContainsString("DTSTAMP:20261101T120000Z\r\n", $ics);
        $this->assertStringContainsString("DTSTART:20270310T090000Z\r\n", $ics);
        $this->assertStringContainsString("DTEND:20270310T163000Z\r\n", $ics);
        $this->assertStringContainsString('SUMMARY:Sepsis Study Day & Q&A\, part 1\; room 3', $ics);
        $this->assertStringContainsString('LOCATION:STRIVE\, JCUH', $ics);
        $this->assertStringContainsString('DESCRIPTION:Bring your badge.\n\nhttps://events.example', $ics);
        $this->assertStringEndsWith("END:VCALENDAR\r\n", $ics);
    }

    public function testAllDayEventEndsTheNextDay(): void
    {
        $ics = IcsGenerator::generate(['id' => 1, 'title' => 'Day', 'addTime' => 0, 'startTime' => mktime(0, 0, 0, 3, 10, 2027), 'endTime' => mktime(23, 59, 59, 3, 11, 2027)], self::URL, 0);

        $this->assertStringContainsString("DTSTART;VALUE=DATE:20270310\r\n", $ics);
        $this->assertStringContainsString("DTEND;VALUE=DATE:20270312\r\n", $ics);
    }

    public function testLongLinesAreFolded(): void
    {
        $ics = IcsGenerator::generate(['id' => 1, 'title' => str_repeat('Palliative ', 20), 'addTime' => 0, 'startTime' => 0, 'endTime' => 0], self::URL, 0);

        foreach (explode("\r\n", $ics) as $line) {
            $this->assertLessThanOrEqual(75, \strlen($line));
        }

        $this->assertStringContainsString("\r\n ", $ics);
    }
}
