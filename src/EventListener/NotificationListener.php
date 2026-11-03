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

use Codefog\EventsSubscriptionsBundle\Event\SubscribeEvent;
use Codefog\EventsSubscriptionsBundle\Event\UnsubscribeEvent;
use Codefog\EventsSubscriptionsBundle\EventConfigFactory;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsListUpdateType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsSubscribeType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsUnsubscribeType;
use Codefog\EventsSubscriptionsBundle\NotificationSender;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\EventsSubscriptionsBundle\SubscriptionFactory;
use Codefog\EventsSubscriptionsBundle\WaitingList;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\System;

class NotificationListener
{
    /**
     * @var EventConfigFactory
     */
    private $eventConfigFactory;

    /**
     * @var NotificationSender
     */
    private $sender;

    /**
     * @var SubscriptionFactory
     */
    private $subscriptionFactory;

    /**
     * NotificationListener constructor.
     */
    public function __construct()
    {
        $this->eventConfigFactory = Services::getEventConfigFactory();
        $this->sender = Services::getNotificationSender();
        $this->subscriptionFactory = Services::getSubscriptionFactory();
    }

    /**
     * On subscribe to event
     *
     * @param SubscribeEvent $event
     */
    public function onSubscribe(SubscribeEvent $event)
    {
        $extras = $event->getExtras();

        // Do not set notification if explicitly set not to do so
        if (isset($extras['notification']) && $extras['notification'] === false) {
            return;
        }

        $this->sender->sendByType(EventsSubscriptionsSubscribeType::NAME, $event->getModel());
    }

    /**
     * On unsubscribe to event
     *
     * @param UnsubscribeEvent $event
     */
    public function onUnsubscribe(UnsubscribeEvent $event)
    {
        $this->sender->sendByType(EventsSubscriptionsUnsubscribeType::NAME, $event->getModel());
        $this->promoteFromWaitingList($event);
    }

    /**
     * Tell the people who moved up from the waiting list because a booking went away. Also used when an unpaid card
     * booking expires, which sends no cancellation e-mail.
     *
     * @param UnsubscribeEvent $event
     */
    public function promoteFromWaitingList(UnsubscribeEvent $event)
    {
        foreach ($this->getWaitingListPromotedSubscriptions($event) as $subscriptionModel) {
            $eventModel = $subscriptionModel->getEvent();
            $subscription = $this->subscriptionFactory->createFromModel($subscriptionModel);

            // Log the event
            System::getContainer()->get('monolog.logger.contao')->info(
                sprintf('%s has promoted from a waiting list to a subscribers list of the event "%s" (ID %s)', strip_tags($subscription->getFrontendLabel()), $eventModel->title, $eventModel->id),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)],
            );

            // Send the notification
            $this->sender->sendByType(EventsSubscriptionsListUpdateType::NAME, $subscriptionModel);
        }
    }

    /**
     * Get the subscriptions that have moved from the waiting list to the subscriber list because of the unsubscription
     *
     * @param UnsubscribeEvent $event
     *
     * @return list<SubscriptionModel>
     */
    private function getWaitingListPromotedSubscriptions(UnsubscribeEvent $event): array
    {
        $unsubscribedModel = $event->getModel();

        if (!$unsubscribedModel->numberOfParticipants) {
            return [];
        }

        $eventConfig = $this->eventConfigFactory->create($unsubscribedModel->pid);

        // Nothing to promote if the event has no waiting list or no limit
        if (!$eventConfig->hasWaitingList() || ($maxSubscriptions = $eventConfig->getMaximumSubscriptions()) === 0) {
            return [];
        }

        $bookings = array_map(
            static fn (array $row) => ['id' => (int) $row['id'], 'places' => (int) $row['places']],
            Database::getInstance()->prepare('SELECT id, numberOfParticipants AS places FROM tl_calendar_events_subscription WHERE pid=? AND numberOfParticipants>0 ORDER BY dateCreated, id')->execute($unsubscribedModel->pid)->fetchAllAssoc(),
        );

        $models = [];

        foreach (WaitingList::promoted($bookings, (int) $unsubscribedModel->id, $maxSubscriptions) as $id) {
            if (null !== ($model = SubscriptionModel::findByPk($id))) {
                $models[] = $model;
            }
        }

        return $models;
    }
}
