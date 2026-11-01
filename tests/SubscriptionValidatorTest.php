<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\SubscriptionValidator;
use PHPUnit\Framework\TestCase;

class SubscriptionValidatorTest extends TestCase
{
    public function testNoDomainsAllowsEverything(): void
    {
        $this->assertTrue((new SubscriptionValidator())->isEmailDomainAllowed('anyone@example.com', []));
    }

    public function testBareAndDottedEntriesAllowDomainAndSubdomains(): void
    {
        $v = new SubscriptionValidator();

        foreach (['nhs.net', '.nhs.net', ' NHS.net '] as $entry) {
            $this->assertTrue($v->isEmailDomainAllowed('a.person@nhs.net', [$entry]), $entry);
            $this->assertTrue($v->isEmailDomainAllowed('a.person@trust.nhs.net', [$entry]), $entry);
            $this->assertTrue($v->isEmailDomainAllowed('A.Person@NHS.NET', [$entry]), $entry);
        }
    }

    public function testAtEntryAllowsExactDomainOnly(): void
    {
        $v = new SubscriptionValidator();
        $this->assertTrue($v->isEmailDomainAllowed('a.person@nhs.net', ['@nhs.net']));
        $this->assertFalse($v->isEmailDomainAllowed('a.person@trust.nhs.net', ['@nhs.net']));
    }

    public function testLookAlikeDomainsAreRejected(): void
    {
        $v = new SubscriptionValidator();

        foreach (['someone@notnhs.net', 'someone@nhs.net.evil.com', 'someone@fakenhs.net', 'nhs.net@gmail.com'] as $email) {
            $this->assertFalse($v->isEmailDomainAllowed($email, ['nhs.net', '@nhs.net', '.nhs.net']), $email);
        }
    }

    public function testAnyOfSeveralDomainsMatches(): void
    {
        $v = new SubscriptionValidator();
        $this->assertTrue($v->isEmailDomainAllowed('someone@dept.gov.uk', ['nhs.net', 'gov.uk']));
        $this->assertFalse($v->isEmailDomainAllowed('someone@gmail.com', ['nhs.net', 'gov.uk']));
    }

    public function testEmptyEntriesAndMalformedAddressesAreHandled(): void
    {
        $v = new SubscriptionValidator();
        $this->assertTrue($v->isEmailDomainAllowed('someone@example.com', ['', ' ']));
        $this->assertFalse($v->isEmailDomainAllowed('no-at-sign', ['nhs.net']));
        $this->assertFalse($v->isEmailDomainAllowed('someone@', ['nhs.net']));
    }
}
