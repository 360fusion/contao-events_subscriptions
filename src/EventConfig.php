<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle;

use Codefog\EventsSubscriptionsBundle\Model\EventGroupModel;
use Codefog\EventsSubscriptionsBundle\Model\PaymentProviderModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentDecision;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\StringUtil;

class EventConfig
{
    /**
     * @var CalendarModel
     */
    private $calendar;

    /**
     * @var CalendarEventsModel
     */
    private $event;

    /**
     * @var array
     */
    private $extras = [];

    private EventGroupModel|false|null $eventGroup = false;

    /**
     * EventConfig constructor.
     *
     * @param CalendarModel       $calendar
     * @param CalendarEventsModel $event
     */
    public function __construct(CalendarModel $calendar, CalendarEventsModel $event)
    {
        $this->calendar = $calendar;
        $this->event = $event;
    }

    /**
     * Get the config value
     *
     * @param string $key
     */
    public function get($key)
    {
        return $this->getSettingsSource('subscription_override')->$key;
    }

    /**
     * Get the notification ID of a notification field (e.g. "subscription_subscribeNotification")
     */
    public function getNotification(string $field): int
    {
        return (int) ($this->getSettingsSource('subscription_customNotifications')->$field ?: $this->calendar->$field);
    }

    /**
     * Price in pence, 0 for free
     */
    public function getPrice(): int
    {
        return (int) $this->getSettingsSource('subscription_customPayment')->subscription_price;
    }

    /**
     * The payment methods that can be offered (card only with a provider), empty if the event is free
     *
     * @return list<string>
     */
    public function getAvailablePaymentMethods(): array
    {
        if (!Features::payments()) {
            return [];
        }

        return PaymentDecision::availableMethods($this->getPrice(), StringUtil::deserialize($this->getSettingsSource('subscription_customPayment')->subscription_paymentMethods, true), null !== $this->getPaymentProvider());
    }

    /**
     * The payment provider, falling back to the calendar's
     */
    public function getPaymentProvider(): PaymentProviderModel|null
    {
        $id = (int) ($this->getSettingsSource('subscription_customPayment')->subscription_paymentProvider ?: $this->calendar->subscription_paymentProvider);

        return $id ? PaymentProviderModel::findByPk($id) : null;
    }

    /**
     * @return list<string>
     */
    public function getFreeEmailDomains(): array
    {
        return array_values(array_filter(StringUtil::trimsplit(',', (string) $this->getSettingsSource('subscription_customPayment')->subscription_freeEmailDomains)));
    }

    /**
     * Get the event group of the event, if any
     */
    public function getEventGroup(): EventGroupModel|null
    {
        if (false === $this->eventGroup) {
            $this->eventGroup = $this->event->eventGroup ? EventGroupModel::findByPk($this->event->eventGroup) : null;
        }

        return $this->eventGroup;
    }

    /**
     * The event, its event group or the calendar: the first one that has the override turned on
     */
    private function getSettingsSource(string $override): object
    {
        if ($this->event->$override) {
            return $this->event;
        }

        if (($group = $this->getEventGroup()) !== null && $group->$override) {
            return $group;
        }

        return $this->calendar;
    }

    /**
     * Get the calendar model
     *
     * @return CalendarModel|null
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Get the event model
     *
     * @return CalendarEventsModel|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get the exrta data.
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set the extra data.
     *
     * @param array $extras
     *
     * @return EventConfig
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Return true if the event can be subscribed to
     *
     * @return bool
     */
    public function canSubscribe()
    {
        return $this->calendar->subscription_enable ? true : false;
    }

    /**
     * Return true if calendar has reminders
     *
     * @return bool
     */
    public function hasReminders()
    {
        return $this->calendar->subscription_reminders ? true : false;
    }

    /**
     * Return true if subscribers can opt out of the reminders
     */
    public function hasReminderOptOut(): bool
    {
        return $this->hasReminders() && !$this->get('subscription_disableReminders');
    }

    /**
     * Get the allowed subscription types
     *
     * @return array
     */
    public function getAllowedSubscriptionTypes()
    {
        return StringUtil::deserialize($this->get('subscription_types'), true);
    }

    /**
     * Get the e-mail domains guests may subscribe with (empty for all). Events use their calendar's list unless they
     * have a custom one, independent of overriding the other subscription settings.
     *
     * @return array<string>
     */
    public function getAllowedEmailDomains(): array
    {
        $source = $this->getSettingsSource('subscription_customEmailDomains');

        // Empty rows of the list wizard are saved as empty entries
        return array_values(array_filter(StringUtil::trimsplit(',', (string) $source->subscription_emailDomains)));
    }

    /**
     * Return true if a guest e-mail address may subscribe to an event only once
     */
    public function hasUniqueGuestEmails(): bool
    {
        return (bool) $this->get('subscription_uniqueEmails');
    }

    /**
     * Get the maximum subscriptions
     *
     * @return int
     */
    public function getMaximumSubscriptions()
    {
        return (int)$this->get('subscription_maximum');
    }

    /**
     * Get the last possible time of subscription
     *
     * @return int
     */
    public function getSubscribeEndTime()
    {
        return $this->calculateTimeOffset($this->event->startTime, $this->get('subscription_subscribeEndTime'));
    }

    /**
     * Get the last possible time of unsubscription
     *
     * @return int
     */
    public function getUnsubscribeEndTime()
    {
        return $this->calculateTimeOffset($this->event->startTime, $this->get('subscription_unsubscribeEndTime'));
    }

    /**
     * Return true if the event has a waiting list
     *
     * @return bool
     */
    public function hasWaitingList()
    {
        return $this->get('subscription_waitingList') ? true : false;
    }

    /**
     * Get the waiting list limit
     *
     * @return int
     */
    public function getWaitingListLimit()
    {
        return (int)$this->get('subscription_waitingListLimit');
    }

    /**
     * Return true if the user can provide a number of participants
     *
     * @return bool
     */
    public function canProvideNumberOfParticipants()
    {
        return $this->get('subscription_numberOfParticipants') ? true : false;
    }

    public function getNumberOfParticipantsLimit()
    {
        return (int) $this->get('subscription_numberOfParticipantsLimit');
    }

    /**
     * Return true if the event has a member groups limit
     *
     * @return bool
     */
    public function hasMemberGroupsLimit()
    {
        return $this->get('subscription_memberGroupsLimit') ? true : false;
    }

    /**
     * Get the allowed member groups
     *
     * @return array
     */
    public function getMemberGroups()
    {
        return StringUtil::deserialize($this->get('subscription_memberGroups'), true);
    }

    /**
     * Calculate the time offset
     *
     * @param int    $time
     * @param string $data
     *
     * @return int
     */
    private function calculateTimeOffset($time, $data)
    {
        $data = StringUtil::deserialize($data, true);

        if ($data['value'] ?? null) {
            $time = (int)strtotime(sprintf('-%s %s', $data['value'], $data['unit']), $time);
        }

        return $time;
    }

    /**
     * Create the instance by event ID
     *
     * @param int $eventId
     *
     * @return EventConfig
     *
     * @throws \InvalidArgumentException
     */
    public static function create($eventId)
    {
        if (($event = CalendarEventsModel::findByPk($eventId)) === null) {
            throw new \InvalidArgumentException(sprintf('The event ID "%s" does not exist', $eventId));
        }

        if (($calendar = CalendarModel::findByPk($event->pid)) === null) {
            throw new \InvalidArgumentException(sprintf('The calendar ID "%s" does not exist', $event->pid));
        }

        return new static($calendar, $event);
    }
}
