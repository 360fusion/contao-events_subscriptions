<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsListUpdateType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsReminderType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsSubscribeType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsUnsubscribeType;
use Codefog\EventsSubscriptionsBundle\Backend\UpcomingController;
use Contao\Backend;
use Contao\Database;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;

class CalendarContainer
{
    /**
     * Show the number of calendars, live events and their subscriptions above the calendar list
     */
    public function addSubscriptionsSummary(): void
    {
        if (Input::get('act') || Input::get('key')) {
            return;
        }

        $db = Database::getInstance();
        $calendars = (int) $db->query('SELECT COUNT(*) AS total FROM tl_calendar')->total;
        $events = (int) $db->query('SELECT COUNT(*) AS total FROM tl_calendar_events WHERE '.UpcomingController::LIVE_EVENTS_WHERE)->total;
        $subscriptions = (int) $db->query('SELECT COUNT(*) AS total FROM tl_calendar_events_subscription WHERE pid IN (SELECT id FROM tl_calendar_events WHERE '.UpcomingController::LIVE_EVENTS_WHERE.')')->total;

        Message::addRaw(sprintf(
            '<p class="tl_info">%s &nbsp;<a href="%s">%s</a></p>',
            sprintf($GLOBALS['TL_LANG']['tl_calendar']['subscriptions_summary'], number_format($calendars), number_format($events), number_format($subscriptions)),
            StringUtil::ampersand(Backend::addToUrl('key=subscriptions_upcoming')),
            $GLOBALS['TL_LANG']['tl_calendar']['subscriptions_summary.link'],
        ));
    }

    /**
     * Get the "subscriptions overview" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function getSubscriptionsOverviewButton(array $row, $href, $label, $title, $icon, $attributes)
    {
        if (!$row['subscription_enable']) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * Get the notifications
     *
     * @return array
     */
    public function getNotifications()
    {
        return $this->getNotificationsByType(EventsSubscriptionsReminderType::NAME);
    }

    /**
     * Get the subscribe notifications
     *
     * @return array
     */
    public function getSubscribeNotifications()
    {
        return $this->getNotificationsByType(EventsSubscriptionsSubscribeType::NAME);
    }

    /**
     * Get the unsubscribe notifications
     *
     * @return array
     */
    public function getUnsubscribeNotifications()
    {
        return $this->getNotificationsByType(EventsSubscriptionsUnsubscribeType::NAME);
    }

    /**
     * Get the list update notifications
     *
     * @return array
     */
    public function getListUpdateNotifications()
    {
        return $this->getNotificationsByType(EventsSubscriptionsListUpdateType::NAME);
    }

    /**
     * Get the notifications by type
     *
     * @param string $type
     *
     * @return array
     */
    private function getNotificationsByType($type)
    {
        $notifications = [];
        $records = Database::getInstance()->prepare("SELECT id, title FROM tl_nc_notification WHERE type=? ORDER BY title")->execute($type);

        while ($records->next()) {
            $notifications[$records->id] = $records->title;
        }

        return $notifications;
    }
}
