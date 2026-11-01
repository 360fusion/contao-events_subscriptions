<?php

namespace Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\EmailTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;

class EventsSubscriptionsTrainerType implements NotificationTypeInterface
{
    public const NAME = 'events_subscriptions_trainer';

    public function __construct(private TokenDefinitionFactoryInterface $factory)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(AnythingTokenDefinition::class, 'calendar_*', ''),
            $this->factory->create(AnythingTokenDefinition::class, 'event_*', ''),

            $this->factory->create(EmailTokenDefinition::class, 'recipient_email', ''),

            $this->factory->create(TextTokenDefinition::class, 'days_before_event', ''),
            $this->factory->create(TextTokenDefinition::class, 'event_link', ''),
            $this->factory->create(TextTokenDefinition::class, 'event_ics_link', ''),
            $this->factory->create(TextTokenDefinition::class, 'signature', ''),
            $this->factory->create(TextTokenDefinition::class, 'signature_html', ''),
            $this->factory->create(TextTokenDefinition::class, 'attendee_list', ''),
            $this->factory->create(TextTokenDefinition::class, 'attendee_count', ''),
            $this->factory->create(TextTokenDefinition::class, 'attendee_places', ''),
        ];
    }
}
