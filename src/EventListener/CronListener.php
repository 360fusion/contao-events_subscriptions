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

use Codefog\EventsSubscriptionsBundle\Payment\PaymentManager;
use Codefog\EventsSubscriptionsBundle\Mailings;
use Codefog\EventsSubscriptionsBundle\Retention;
use Contao\Config;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;

class CronListener
{
    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    /**
     * Execute the tasks on hourly job
     */
    #[AsCronJob('hourly')]
    public function onHourlyJob(): void
    {
        $this->framework->initialize();

        $remindersSent = Services::getAutomator()->sendReminders();
        $mailings = new Mailings();

        foreach (['trainer packs' => $mailings->sendTrainerPacks(), 'after-the-event e-mails' => $mailings->sendFollowUps()] as $label => $count) {
            if ($count > 0) {
                System::getContainer()->get('monolog.logger.contao')->info(
                    sprintf('A total number of %s %s have been sent', $count, $label),
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
                );
            }
        }

        if ($remindersSent > 0) {
            System::getContainer()->get('monolog.logger.contao')->info(
                sprintf('A total number of %s event reminders have been sent', $remindersSent),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
            );
        }
    }

    /**
     * Free the places of card checkouts that were not completed within the hold time of Booking settings › Payments
     */
    #[AsCronJob('minutely')]
    public function onMinutelyJob(): void
    {
        $this->framework->initialize();

        $manager = new PaymentManager();
        $expired = $manager->expirePending();

        if (($unconfirmed = $manager->expireUnconfirmed()) > 0) {
            System::getContainer()->get('monolog.logger.contao')->info(
                sprintf('%s bookings were cancelled because the e-mail address was not confirmed', $unconfirmed),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
            );
        }

        if ($expired > 0) {
            System::getContainer()->get('monolog.logger.contao')->info(
                sprintf('%s unpaid bookings were cancelled because the payment was not completed', $expired),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::CRON)],
            );
        }
    }

    /**
     * Anonymise or delete old bookings, if switched on in Events › Settings › Booking settings
     */
    #[AsCronJob('daily')]
    public function onDailyJob(): void
    {
        $this->framework->initialize();

        if (null !== ($cutoff = Retention::cutoff())) {
            (new Retention())->run($cutoff, (string) Config::get('eventsSubscriptionsRetentionAction') ?: 'anonymise');
        }
    }
}
