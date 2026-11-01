<?php

namespace Codefog\EventsSubscriptionsBundle;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\System;

/**
 * Removes the personal data of bookings for events that ended a while ago (Events › Setup › Booking settings)
 */
class Retention
{
    public const ANONYMISED = '(anonymised)';

    /**
     * Timestamp before which events count as old, null if retention is switched off
     */
    public static function cutoff(int|null $now = null): int|null
    {
        if (!Config::get('eventsSubscriptionsRetention') || ($months = (int) Config::get('eventsSubscriptionsRetentionMonths')) < 1) {
            return null;
        }

        return strtotime(sprintf('-%d months', $months), $now ?? time());
    }

    /**
     * Number of bookings that still have personal data and are due
     */
    public function countDue(int $cutoff): int
    {
        return (int) Database::getInstance()
            ->prepare('SELECT COUNT(*) AS n FROM tl_calendar_events_subscription s JOIN tl_calendar_events e ON e.id=s.pid WHERE GREATEST(e.startTime, e.endTime)<? AND s.lastname!=?')
            ->execute($cutoff, self::ANONYMISED)
            ->n;
    }

    /**
     * Anonymise or delete the due bookings. Places, attendance and payment amounts are kept when anonymising.
     *
     * @return int The number of bookings changed
     */
    public function run(int $cutoff, string $action): int
    {
        $db = Database::getInstance();
        $ids = $db->prepare('SELECT s.id FROM tl_calendar_events_subscription s JOIN tl_calendar_events e ON e.id=s.pid WHERE GREATEST(e.startTime, e.endTime)<? AND s.lastname!=?')->execute($cutoff, self::ANONYMISED)->fetchEach('id');

        if (!$ids) {
            return 0;
        }

        foreach (array_chunk(array_map('intval', $ids), 500) as $chunk) {
            $in = implode(',', $chunk);

            // The payments ledger keeps the amounts, without the name and e-mail address
            if ($db->tableExists('tl_calendar_events_payment')) {
                $db->query("UPDATE tl_calendar_events_payment SET name='".self::ANONYMISED."', email='', purchaseOrder='' WHERE subscription IN ($in)");
            }

            if ('delete' === $action) {
                $db->query("DELETE FROM tl_calendar_events_subscription WHERE id IN ($in)");
            } else {
                $db->query('UPDATE tl_calendar_events_subscription SET '.implode(', ', $this->getAnonymiseAssignments())." WHERE id IN ($in)");
            }
        }

        System::getContainer()->get('monolog.logger.contao')->info(
            sprintf('%s %d bookings of events that ended before %s', 'delete' === $action ? 'Deleted' : 'Anonymised', \count($ids), date('Y-m-d', $cutoff)),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
        );

        return \count($ids);
    }

    /**
     * The personal fields of a guest booking, plus text columns no longer defined in the DCA (e.g. left over from an
     * older extension that stored the answers in their own columns)
     *
     * @return list<string> SQL assignments
     */
    private function getAnonymiseAssignments(): array
    {
        Controller::loadDataContainer('tl_calendar_events_subscription');

        $db = Database::getInstance();
        $defined = array_keys($GLOBALS['TL_DCA']['tl_calendar_events_subscription']['fields'] ?? []);
        $set = ["firstname=''", "lastname='".self::ANONYMISED."'", "email=''", 'registrationFields=NULL', "unsubscribeToken=''", 'tstamp='.time()];

        $columns = $db->query("SELECT COLUMN_NAME AS name, DATA_TYPE AS type, IS_NULLABLE AS nullable FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='tl_calendar_events_subscription'")->fetchAllAssoc();

        foreach ($columns as $column) {
            if (!\in_array($column['name'], $defined, true) && \in_array(strtolower($column['type']), ['varchar', 'char', 'text', 'mediumtext', 'longtext', 'tinytext', 'json', 'blob'], true)) {
                $set[] = $db->quoteIdentifier($column['name']).('YES' === $column['nullable'] ? '=NULL' : "=''");
            }
        }

        return $set;
    }
}
