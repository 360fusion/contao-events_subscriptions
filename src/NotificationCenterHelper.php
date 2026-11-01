<?php

namespace Codefog\EventsSubscriptionsBundle;

use Terminal42\NotificationCenterBundle\NotificationCenter;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\LanguageConfigStamp;

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

    /**
     * Send a test of a notification to one address only: its recipients, CC and BCC are replaced
     *
     * @return int The number of delivered messages
     */
    public function sendTest(int $id, array $tokens, string $email): int
    {
        $delivered = 0;
        $stamps = $this->notificationCenter->createBasicStampsForNotification($id, $tokens);

        foreach ($this->notificationCenter->createParcelsForNotification($id, $stamps) as $parcel) {
            $language = $parcel->getStamp(LanguageConfigStamp::class)?->languageConfig;

            if (null === $language) {
                continue;
            }

            $parcel = $parcel->withStamp(new LanguageConfigStamp($language
                ->withParameter('recipients', $email)
                ->withParameter('email_recipient_cc', '')
                ->withParameter('email_recipient_bcc', '')
            ));

            if ($this->notificationCenter->sendParcel($parcel)->wasDelivered()) {
                ++$delivered;
            }
        }

        return $delivered;
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
