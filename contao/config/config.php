<?php

// Backend modules
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_subscription';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_group';
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_event_group';

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
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_upcoming'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\UpcomingController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_people'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\SubscribersController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_peopleAll'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\SubscribersController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_register'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\RegisterController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_person'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\PersonController',
    'run',
];
$GLOBALS['BE_MOD']['content']['calendar']['subscriptions_overview'] = [
    'Codefog\EventsSubscriptionsBundle\Backend\OverviewController',
    'run',
];

// The "Events" group of the back end menu: the calendars (core module, same URL do=calendar) and the booking tools
$calendarModule = $GLOBALS['BE_MOD']['content']['calendar'];
unset($GLOBALS['BE_MOD']['content']['calendar']);

\Contao\ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 1, ['events_subscriptions' => [
    'calendar' => $calendarModule,
    'event_upcoming' => [
        'callback' => 'Codefog\EventsSubscriptionsBundle\Backend\Module\UpcomingModule',
    ],
    'event_subscribers' => [
        'callback' => 'Codefog\EventsSubscriptionsBundle\Backend\Module\SubscribersModule',
    ],
    'event_emails' => [
        'tables' => ['tl_nc_notification', 'tl_nc_message', 'tl_nc_language'],
        'event_emailTest' => ['Codefog\EventsSubscriptionsBundle\Backend\EventEmailTestController', 'run'],
    ],
    'event_payments' => [
        'tables' => ['tl_calendar_events_payment'],
        'payment_refund' => ['Codefog\EventsSubscriptionsBundle\Backend\PaymentController', 'run'],
        'payment_markPaid' => ['Codefog\EventsSubscriptionsBundle\Backend\PaymentController', 'run'],
    ],
    'event_reports' => [
        'callback' => 'Codefog\EventsSubscriptionsBundle\Backend\Module\ReportsModule',
    ],
    'event_setup' => [
        'callback' => 'Codefog\EventsSubscriptionsBundle\Backend\Module\SetupModule',
    ],
    // Opened from Setup
    'event_settings' => [
        'tables' => ['tl_events_subscriptions_settings'],
        'hideInNavigation' => true,
    ],
    'event_retention' => [
        'callback' => 'Codefog\EventsSubscriptionsBundle\Backend\Module\RetentionModule',
        'hideInNavigation' => true,
    ],
    'event_health' => [
        'callback' => 'Codefog\EventsSubscriptionsBundle\Backend\Module\HealthModule',
        'hideInNavigation' => true,
    ],
    'event_fields' => [
        'tables' => ['tl_calendar_events_subscription_field'],
        'hideInNavigation' => true,
    ],
    'event_payment_providers' => [
        'tables' => ['tl_calendar_events_payment_provider'],
        'hideInNavigation' => true,
    ],
]]);

unset($calendarModule);

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

$GLOBALS['TL_HOOKS']['loadLanguageFile'][] = [
    'Codefog\EventsSubscriptionsBundle\EventListener\LanguageListener',
    'onLoadLanguageFile',
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
$GLOBALS['TL_CRON']['daily'][] = ['Codefog\EventsSubscriptionsBundle\EventListener\CronListener', 'onDailyJob'];

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
