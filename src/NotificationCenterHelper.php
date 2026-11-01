<?php

namespace Codefog\EventsSubscriptionsBundle;

use Terminal42\NotificationCenterBundle\NotificationCenter;

class NotificationCenterHelper
{
    public function __construct(private NotificationCenter $notificationCenter)
    {
    }

    /**
     * @return list<string> The token names of a notification type, wildcards like "event_*" included
     */
    public function getTokenNames(string $type): array
    {
        return array_map(static fn ($definition) => $definition->getTokenName(), $this->notificationCenter->getTokenDefinitionsForNotificationType($type));
    }

    public function sendNotification(int $id, array $tokens, string|null $locale = null): int
    {
        $locale ??= $tokens['subscription_language'] ?? '';
        $total = 0;
        $receiptCollection = $this->notificationCenter->sendNotification($id, $tokens, $locale);

        foreach ($receiptCollection as $receipt) {
            if ($receipt->wasDelivered()) {
                $total++;
            }
        }

        return $total;
    }

    public function sendNotificationsByType(string $type, array $tokens, string|null $locale = null): void
    {
        foreach (array_keys($this->getNotificationsByType($type)) as $id) {
            $this->notificationCenter->sendNotification($id, $tokens, $locale);
        }
    }

    public function getNotificationsByType(string $type): array
    {
        return $this->notificationCenter->getNotificationsForNotificationType($type);
    }
}
