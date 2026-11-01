<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\Email\EmailTags;
use PHPUnit\Framework\TestCase;

class EmailTagsTest extends TestCase
{
    public function testOnlyTheTokensOfTheTypeAreOffered(): void
    {
        $groups = EmailTags::forType(
            ['event_*', 'subscription_*', 'event_link', 'payment_link'],
            ['subscription_field_job_role' => 'Job Role'],
            ['subscription_firstname' => 'First name', 'event_title' => 'Event title'],
        );

        $this->assertSame('First name', $groups['booking']['subscription_firstname']);
        $this->assertSame('Job Role', $groups['booking']['subscription_field_job_role']);
        $this->assertSame('Event title', $groups['event']['event_title']);
        $this->assertArrayNotHasKey('calendar_title', $groups['event']);
        $this->assertSame(['event_link' => 'event_link', 'event_ics_link' => 'event_ics_link'], $groups['links']); // event_* covers both
        $this->assertSame(['payment_link' => 'payment_link'], $groups['payment']);
    }

    public function testNoTokensNoGroups(): void
    {
        $this->assertSame([], EmailTags::forType([], ['subscription_field_x' => 'X'], []));
    }
}
