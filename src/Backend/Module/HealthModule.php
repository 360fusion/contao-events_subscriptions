<?php

namespace Codefog\EventsSubscriptionsBundle\Backend\Module;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Retention;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Database;
use Contao\StringUtil;
use Contao\System;

/**
 * Events › Setup › Health check: settings that probably need attention
 */
class HealthModule
{
    private const LIMIT = 10;

    private const NOTIFICATION_FIELDS = ['subscription_subscribeNotification', 'subscription_unsubscribeNotification', 'subscription_listUpdateNotification', 'subscription_notification'];

    public function __construct(mixed $dc = null)
    {
    }

    public function generate(): string
    {
        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_events_subscriptions_settings');

        $template = new BackendTemplate('be_events_subscriptions_health');
        $template->checks = self::getChecks();

        return $template->parse();
    }

    /**
     * @return list<array{key: string, count: int, items: list<array{label: string, href: string}>}>
     */
    public static function getChecks(): array
    {
        $db = Database::getInstance();
        $router = System::getContainer()->get('router');
        $url = static fn (array $query) => $router->generate('contao_backend', $query);
        $checks = [];

        // E-mails that are used but send nothing
        $rows = [];
        $notifications = $db->query("SELECT n.id, n.title, (SELECT COUNT(*) FROM tl_nc_message m WHERE m.pid=n.id AND m.published=1 AND EXISTS (SELECT 1 FROM tl_nc_language l WHERE l.pid=m.id)) AS sending FROM tl_nc_notification n WHERE n.type LIKE 'events_subscription%'")->fetchAllAssoc();

        foreach ($notifications as $notification) {
            if (!$notification['sending'] && self::countNotificationUsage((int) $notification['id']) > 0) {
                $rows[] = ['label' => StringUtil::decodeEntities($notification['title']), 'href' => $url(['do' => 'event_emails', 'act' => 'edit', 'id' => $notification['id']])];
            }
        }

        $checks[] = ['key' => 'emails', 'count' => \count($rows), 'items' => $rows];

        // Calendars with bookings but no confirmation e-mail
        $rows = array_map(
            static fn ($row) => ['label' => $row['title'], 'href' => $url(['do' => 'calendar', 'act' => 'edit', 'id' => $row['id']])],
            $db->query('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 AND subscription_subscribeNotification=0 ORDER BY title')->fetchAllAssoc(),
        );
        $checks[] = ['key' => 'noConfirmation', 'count' => \count($rows), 'items' => $rows];

        // Upcoming events
        $upcoming = $db->query('SELECT e.* FROM tl_calendar_events e JOIN tl_calendar c ON c.id=e.pid WHERE c.subscription_enable=1 AND e.published=1 AND GREATEST(e.startTime, e.endTime)>=UNIX_TIMESTAMP() ORDER BY e.startTime')->fetchAllAssoc();
        $calendarsWithGroups = $db->query('SELECT DISTINCT pid FROM tl_calendar_event_group')->fetchEach('pid');
        $noGroup = $noLimit = $noPayment = [];
        $calendars = [];

        foreach ($upcoming as $event) {
            $item = ['label' => StringUtil::decodeEntities($event['title']), 'href' => $url(['do' => 'calendar', 'table' => 'tl_calendar_events', 'act' => 'edit', 'id' => $event['id']])];

            if (!$event['eventGroup'] && \in_array($event['pid'], $calendarsWithGroups)) {
                $noGroup[] = $item;
            }

            $config = new EventConfig($calendars[$event['pid']] ??= CalendarModel::findByPk($event['pid']), (new CalendarEventsModel())->setRow($event));

            if (0 === $config->getMaximumSubscriptions()) {
                $noLimit[] = $item;
            }

            // A price is set, but none of the chosen payment methods can be offered (card without a provider)
            if ($config->getPrice() > 0 && StringUtil::deserialize($config->get('subscription_paymentMethods'), true) && !$config->getAvailablePaymentMethods()) {
                $noPayment[] = $item;
            }
        }

        $checks[] = ['key' => 'noGroup', 'count' => \count($noGroup), 'items' => $noGroup];
        $checks[] = ['key' => 'noLimit', 'count' => \count($noLimit), 'items' => $noLimit];
        $checks[] = ['key' => 'noPayment', 'count' => \count($noPayment), 'items' => $noPayment];

        // Published courses without upcoming dates
        $rows = array_map(
            static fn ($row) => ['label' => $row['title'].' ('.$row['calendar'].')', 'href' => $url(['do' => 'calendar', 'table' => 'tl_calendar_event_group', 'act' => 'edit', 'id' => $row['id']])],
            $db->query('SELECT g.id, g.title, c.title AS calendar FROM tl_calendar_event_group g JOIN tl_calendar c ON c.id=g.pid WHERE g.published=1 AND NOT EXISTS (SELECT 1 FROM tl_calendar_events e WHERE e.eventGroup=g.id AND e.published=1 AND GREATEST(e.startTime, e.endTime)>=UNIX_TIMESTAMP()) ORDER BY c.title, g.title')->fetchAllAssoc(),
        );
        $checks[] = ['key' => 'emptyGroups', 'count' => \count($rows), 'items' => $rows];

        // Personal data of old bookings
        $cutoff = Retention::cutoff() ?? strtotime(sprintf('-%d months', Retention::months()));
        $checks[] = [
            'key' => null === Retention::cutoff() ? 'retentionOff' : 'retentionDue',
            'count' => (new Retention())->countDue($cutoff),
            'months' => Retention::months(),
            'items' => [
                ['label' => $GLOBALS['TL_LANG']['MOD']['event_retention'][0] ?? 'Old booking data', 'href' => $url(['do' => 'event_retention'])],
                ['label' => $GLOBALS['TL_LANG']['MOD']['event_settings'][0] ?? 'Booking settings', 'href' => $url(['do' => 'event_settings'])],
            ],
        ];

        foreach ($checks as &$check) {
            $check['more'] = max(0, \count($check['items']) - self::LIMIT);
            $check['items'] = \array_slice($check['items'], 0, self::LIMIT);
        }

        return $checks;
    }

    private static function countNotificationUsage(int $id): int
    {
        $db = Database::getInstance();
        $where = implode(' OR ', array_map(static fn ($field) => "$field=$id", self::NOTIFICATION_FIELDS));
        $count = 0;

        foreach (['tl_calendar', 'tl_calendar_event_group', 'tl_calendar_events'] as $table) {
            $count += (int) $db->query("SELECT COUNT(*) AS n FROM $table WHERE $where")->n;
        }

        return $count;
    }
}
