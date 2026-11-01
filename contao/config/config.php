<?php

// Backend modules
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_subscription';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_subscription_field';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_group';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_event_group';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_payment_provider';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_payment';

$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_export'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\ExportController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_exportCalendar'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\ExportCalendarController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_newFromMemberGroup'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\NewFromMemberGroupController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_notification'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\NotificationController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_exportGroup'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\ExportCalendarController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_exportEventGroup'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\ExportCalendarController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['payment_refund'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\PaymentController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['payment_markPaid'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\PaymentController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_upcoming'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\UpcomingController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_overview'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\OverviewController',
    'run',
];

// Frontend modules
$GLOBALS['FE_MOD']['events']['event_list_subscribe'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventListModule';
$GLOBALS['FE_MOD']['events']['event_reader_subscribe'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventReaderModule';
$GLOBALS['FE_MOD']['events']['event_subscribe'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventSubscribeModule';
$GLOBALS['FE_MOD']['events']['event_subscriptions'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventSubscriptionsModule';
$GLOBALS['FE_MOD']['events']['event_navigation'] = 'Codefog\EventsSubscriptionsBundle\FrontendModule\EventNavigationModule';

// Hooks
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptionsBundle\EventDispatcher::EVENT_ON_SUBSCRIBE][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\NotificationListener',
    'onSubscribe',
];
$GLOBALS['TL_HOOKS'][\Codefog\EventsSubscriptionsBundle\EventDispatcher::EVENT_ON_UNSUBSCRIBE][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\NotificationListener',
    'onUnsubscribe',
];

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\RegistrationFieldsListener',
    'onLoadDataContainer',
];

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\InsertTagsListener',
    'onReplace',
];

$GLOBALS['TL_HOOKS']['generatePage'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\PageListener',
    'onGeneratePage',
];

$GLOBALS['TL_HOOKS']['reviseTable'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\TableListener',
    'onReviseTable',
];

$GLOBALS['TL_HOOKS']['getAllEvents'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\EventsListener',
    'onGetAllEvents',
];

$GLOBALS['TL_HOOKS']['getAllEvents'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\EventGroupFilterListener',
    'onGetAllEvents',
];

// Models
$GLOBALS['TL_MODELS']['tl_calendar_events_subscription'] = 'Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel';
$GLOBALS['TL_MODELS']['tl_calendar_events_subscription_field'] = 'Codefog\EventsSubscriptionsBundle\Model\SubscriptionFieldModel';
$GLOBALS['TL_MODELS']['tl_calendar_group'] = 'Codefog\EventsSubscriptionsBundle\Model\CalendarGroupModel';
$GLOBALS['TL_MODELS']['tl_calendar_event_group'] = 'Codefog\EventsSubscriptionsBundle\Model\EventGroupModel';
$GLOBALS['TL_MODELS']['tl_calendar_events_payment_provider'] = 'Codefog\EventsSubscriptionsBundle\Model\PaymentProviderModel';
$GLOBALS['TL_MODELS']['tl_calendar_events_payment'] = 'Codefog\EventsSubscriptionsBundle\Model\PaymentModel';

// Cron jobs
$GLOBALS['TL_CRON']['hourly'][] = ['Codefog\EventsSubscriptionsBundle\EventListener\CronListener', 'onHourlyJob'];
$GLOBALS['TL_CRON']['minutely'][] = ['Codefog\EventsSubscriptionsBundle\EventListener\CronListener', 'onMinutelyJob'];

/**
 * Add the subscription types
 */
\Codefog\EventsSubscriptionsBundle\Services::getSubscriptionFactory()->add(
    'guest',
    'Codefog\EventsSubscriptionsBundle\Subscription\GuestSubscription'
);

\Codefog\EventsSubscriptionsBundle\Services::getSubscriptionFactory()->add(
    'member',
    'Codefog\EventsSubscriptionsBundle\Subscription\MemberSubscription'
);
