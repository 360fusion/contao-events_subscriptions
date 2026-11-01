<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Automator;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentManager;
use Codefog\EventsSubscriptionsBundle\Retention;
use Contao\Config;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;

class CronListener
{
    /**
     * @var Automator
     */
    private $automator;

    /**
     * CronListener constructor.
     */
    public function __construct()
    {
        $this->automator = Services::getAutomator();
    }

    /**
     * Execute the tasks on hourly job
     */
    public function onHourlyJob()
    {
        $remindersSent = $this->automator->sendReminders();

        if ($remindersSent > 0) {
            System::getContainer()->get('monolog.logger.contao')->info(
                sprintf('A total number of %s event reminders have been sent', $remindersSent),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
            );
        }
    }

    /**
     * Free the places of card checkouts that were not completed within 30 minutes
     */
    public function onMinutelyJob(): void
    {
        $expired = (new PaymentManager())->expirePending();

        if ($expired > 0) {
            System::getContainer()->get('monolog.logger.contao')->info(
                sprintf('%s unpaid bookings were cancelled because the payment was not completed', $expired),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
            );
        }
    }

    /**
     * Anonymise or delete old bookings, if switched on in Events › Setup › Booking settings
     */
    public function onDailyJob(): void
    {
        if (null !== ($cutoff = Retention::cutoff())) {
            (new Retention())->run($cutoff, (string) Config::get('eventsSubscriptionsRetentionAction') ?: 'anonymise');
        }
    }
}
