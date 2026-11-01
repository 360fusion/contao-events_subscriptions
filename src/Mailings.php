<?php

namespace Codefog\EventsSubscriptionsBundle;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\Database;
use Contao\System;

/**
 * The e-mails the site sends by itself around an event: the trainer pack the day before and the thank-you (certificate, feedback) the day after.
 * Each is on once an e-mail is chosen in Events › Settings › Booking settings, and sent once for every event or booking.
 */
class Mailings
{
    /**
     * Only events that ended recently get the after-the-event e-mail, so switching it on never writes to people who attended long ago
     */
    private const FOLLOW_UP_DAYS = 3;
    private const FOLLOW_UP_FROM_HOUR = 9;
    private const FOLLOW_UP_PER_RUN = 300;

    /**
     * @return int The number of trainer packs sent
     */
    public function sendTrainerPacks(): int
    {
        if (!($notification = (int) Config::get('eventsSubscriptionsTrainerNotification'))) {
            return 0;
        }

        $now = time();
        $tomorrowEnd = strtotime('tomorrow +1 day') - 1;
        $today = strtotime('tomorrow') - 1;
        $afterSendHour = (int) date('G') >= Features::trainerHour();
        $events = Database::getInstance()
            ->prepare('SELECT e.* FROM tl_calendar_events e JOIN tl_calendar c ON c.id=e.pid WHERE c.subscription_enable=1 AND e.published=1 AND e.trainerPackSent=0 AND e.startTime>? AND e.startTime<=? ORDER BY e.startTime')
            ->execute($now, $tomorrowEnd)
            ->fetchAllAssoc();
        $sent = 0;

        foreach ($events as $row) {
            // Tomorrow's events from the send hour on; an event that is still today and was missed goes out at once
            if (!$afterSendHour && $row['startTime'] > $today) {
                continue;
            }

            $event = (new CalendarEventsModel())->setRow($row);

            if ('' === ($email = $this->getTrainerEmail($row))) {
                continue;
            }

            $config = Services::getEventConfigFactory()->create((int) $row['id']);
            [$list, $count, $places] = $this->getAttendees($config);
            $tokens = Services::getNotificationSender()->getBasicTokens($event) + [
                'recipient_email' => $email,
                'attendee_list' => $list,
                'attendee_count' => $count,
                'attendee_places' => $places,
            ];

            System::getContainer()->get(NotificationCenterHelper::class)->sendNotification($notification, $tokens, '');
            Database::getInstance()->prepare('UPDATE tl_calendar_events SET trainerPackSent=? WHERE id=?')->execute($now, $row['id']);
            ++$sent;
        }

        return $sent;
    }

    /**
     * @return int The number of e-mails sent
     */
    public function sendFollowUps(): int
    {
        if (!($notification = (int) Config::get('eventsSubscriptionsFollowUpNotification')) || (int) date('G') < self::FOLLOW_UP_FROM_HOUR) {
            return 0;
        }

        // Ended before today, within the last few days
        $bookings = Database::getInstance()
            ->prepare("SELECT s.id FROM tl_calendar_events_subscription s JOIN tl_calendar_events e ON e.id=s.pid JOIN tl_calendar c ON c.id=e.pid WHERE c.subscription_enable=1 AND s.followUpSent=0 AND s.unsubscribeToken!='' AND s.lastname!=? AND GREATEST(e.startTime, e.endTime)<? AND GREATEST(e.startTime, e.endTime)>=?".(Features::attendance() ? ' AND s.attended=1' : '')." AND s.paymentStatus NOT IN ('pending', 'failed') ORDER BY s.id")
            ->limit(self::FOLLOW_UP_PER_RUN)
            ->execute(Retention::ANONYMISED, strtotime('today'), strtotime('-'.self::FOLLOW_UP_DAYS.' days', strtotime('today')));
        $factory = Services::getSubscriptionFactory();
        $sent = 0;

        foreach ($bookings->fetchEach('id') as $id) {
            $model = SubscriptionModel::findByPk($id);

            try {
                $subscription = $factory->createFromModel($model);
            } catch (\InvalidArgumentException) {
                continue;
            }

            // Marked first, so a failing e-mail is not retried every hour
            Database::getInstance()->prepare('UPDATE tl_calendar_events_subscription SET followUpSent=? WHERE id=?')->execute(time(), $id);

            if ($subscription->isOnWaitingList() || '' === (string) $subscription->getNotificationEmail()) {
                continue;
            }

            Services::getNotificationSender()->send($notification, $model);
            ++$sent;
        }

        return $sent;
    }

    /**
     * The trainer set on the event, otherwise the one of its course
     */
    private function getTrainerEmail(array $event): string
    {
        $email = (string) $event['subscription_trainerEmail'];

        if ('' === $email && $event['eventGroup']) {
            $email = (string) Database::getInstance()->prepare('SELECT subscription_trainerEmail FROM tl_calendar_event_group WHERE id=?')->execute($event['eventGroup'])->subscription_trainerEmail;
        }

        return trim($email);
    }

    /**
     * @return array{0: string, 1: int, 2: int} The attendee list as text, the number of bookings and the number of places
     */
    private function getAttendees(EventConfig $config): array
    {
        $blocks = [];
        $places = 0;

        foreach (array_filter(Attendees::forEvent($config), static fn (array $row) => !$row['waiting']) as $row) {
            $lines = [sprintf('%d. %s%s', \count($blocks) + 1, $row['name'], $row['email'] ? ' <'.$row['email'].'>' : '')];

            if ($row['places'] > 1) {
                $lines[] = '   '.$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants'].': '.$row['places'];
            }

            foreach ($row['answers'] as [$label, $value]) {
                $lines[] = '   '.$label.': '.$value;
            }

            $blocks[] = implode("\n", $lines);
            $places += $row['places'];
        }

        return [$blocks ? implode("\n\n", $blocks) : $GLOBALS['TL_LANG']['MSC']['events_subscriptions.noAttendees'], \count($blocks), $places];
    }
}
