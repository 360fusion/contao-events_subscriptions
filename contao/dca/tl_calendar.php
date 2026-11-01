<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Doctrine\DBAL\Types\Types;

// Load tl_calendar_events data container
\Contao\Controller::loadDataContainer('tl_calendar_events');
\Contao\System::loadLanguageFile('tl_calendar_events');

// E-mails, payments, subscribers and the setup are in the "Events" group of the back end menu

// Calendar groups (Events › Settings › Booking settings)
if (\Codefog\EventsSubscriptionsBundle\Features::calendarGroups()) {
    \Contao\ArrayUtil::arrayInsert($GLOBALS['TL_DCA']['tl_calendar']['list']['global_operations'], 0, [
        'calendar_groups' => [
            'href' => 'table=tl_calendar_group',
            'icon' => 'folderC.svg',
            'primary' => true,
            'attributes' => 'data-action="contao--scroll-offset#store"',
        ],
    ]);

    // List the calendars under their group
    $GLOBALS['TL_DCA']['tl_calendar']['list']['sorting']['fields'] = ['calendarGroup', 'title'];
    $GLOBALS['TL_DCA']['tl_calendar']['list']['sorting']['flag'] = \Contao\DataContainer::SORT_ASC;

    PaletteManipulator::create()
        ->addField('calendarGroup', 'title_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette('default', 'tl_calendar');
}

$GLOBALS['TL_DCA']['tl_calendar']['fields']['calendarGroup'] = [
    'filter' => \Codefog\EventsSubscriptionsBundle\Features::calendarGroups(),
    'inputType' => 'select',
    'foreignKey' => 'tl_calendar_group.title',
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
];

// Link the calendar titles to their events
$GLOBALS['TL_DCA']['tl_calendar']['list']['label']['label_callback'] = ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'generateLabel'];

// New calendars copy the booking settings of the template calendar (Events › Settings › Booking settings)
$GLOBALS['TL_DCA']['tl_calendar']['config']['oncreate_callback'][] = static fn ($table, $insertId) => (new \Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer())->applyTemplateCalendar((string) $table, (int) $insertId);

// Summary above the calendar list
$GLOBALS['TL_DCA']['tl_calendar']['config']['onload_callback'][] = ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'addSubscriptionsSummary'];

// List operations
// Everyone booked on the calendar's events
$GLOBALS['TL_DCA']['tl_calendar']['list']['operations']['subscriptions_people'] = [
    'href' => 'key=subscriptions_people',
    'icon' => 'member.svg',
    'primary' => true,
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getSubscriptionsOverviewButton'],
];

$GLOBALS['TL_DCA']['tl_calendar']['list']['operations']['subscriptions_overview'] = [
    'href' => 'key=subscriptions_overview',
    'icon' => 'mgroup.svg',
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getSubscriptionsOverviewButton'],
];

// Event groups: the calendar is the category, its event groups are the courses within it
$GLOBALS['TL_DCA']['tl_calendar']['config']['ctable'][] = 'tl_calendar_event_group';

// New calendars use the event page most calendars already use, so editors do not have to know which page it is
$GLOBALS['TL_DCA']['tl_calendar']['fields']['jumpTo']['default'] = static fn () => (int) \Contao\Database::getInstance()
    ->query('SELECT jumpTo FROM tl_calendar WHERE jumpTo>0 AND tstamp>0 GROUP BY jumpTo ORDER BY COUNT(*) DESC LIMIT 1')
    ->jumpTo;

$GLOBALS['TL_DCA']['tl_calendar']['list']['operations']['event_groups'] = [
    'href' => 'table=tl_calendar_event_group',
    'icon' => 'folderC.svg',
    'primary' => true,
];

PaletteManipulator::create()
    ->addField('alias', 'title', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_calendar');

$GLOBALS['TL_DCA']['tl_calendar']['fields']['alias'] = [
    'search' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
    'save_callback' => [
        ['Codefog\EventsSubscriptionsBundle\DataContainer\EventGroupContainer', 'generateAlias'],
    ],
    'sql' => "varchar(255) BINARY NOT NULL default ''",
];

// Palettes
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_enable';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_memberGroupsLimit';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_numberOfParticipants';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'subscription_reminders';

PaletteManipulator::create()
    ->addLegend('subscription_legend', 'title_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('subscription_enable', 'subscription_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_enable'] = 'subscription_types,subscription_emailDomains,subscription_memberGroupsLimit,subscription_maximum,subscription_uniqueEmails,subscription_subscribeEndTime,subscription_unsubscribeEndTime,subscription_numberOfParticipants,subscription_fields,subscription_waitingList,subscription_reminders,subscription_unsubscribeLinkPage,subscription_subscribeNotification,subscription_unsubscribeNotification,subscription_listUpdateNotification,subscription_price,subscription_paymentMethods,subscription_paymentProvider,subscription_freeEmailDomains';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_waitingList'] = 'subscription_waitingListLimit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_memberGroupsLimit'] = 'subscription_memberGroups';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_numberOfParticipants'] = 'subscription_numberOfParticipantsLimit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_reminders'] = 'subscription_time,subscription_days,subscription_notification,subscription_skipWaitingListReminders,subscription_disableReminders';

// Fields
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_enable'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_types'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_emailDomains'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_emailDomains'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_price'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_price'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_paymentMethods'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_paymentMethods'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_paymentProvider'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_paymentProvider'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_freeEmailDomains'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_freeEmailDomains'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_uniqueEmails'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_uniqueEmails'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_disableReminders'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_disableReminders'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_maximum'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subscribeEndTime'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeEndTime'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeEndTime'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeEndTime'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_numberOfParticipants'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipants'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_fields'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_fields'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_numberOfParticipantsLimit'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipantsLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_waitingList'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingList'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_waitingListLimit'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingListLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_memberGroupsLimit'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroupsLimit'];
$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_memberGroups'] = &$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroups'];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_reminders'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_time'] = [
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'rgxp' => 'time', 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_days'] = [
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'maxlength' => 32, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_notification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getNotifications'],
    'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_subscribeNotification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getSubscribeNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeNotification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getUnsubscribeNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_listUpdateNotification'] = [
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarContainer', 'getListUpdateNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_skipWaitingListReminders'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['subscription_unsubscribeLinkPage'] = [
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

// Back end styles (the filter panel scrolls when it is taller than the window)
$GLOBALS['TL_DCA']['tl_calendar']['config']['onload_callback'][] = ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'addBackendStyles'];

// Payments switched off in Events › Settings › Booking settings
if (!\Codefog\EventsSubscriptionsBundle\Features::payments()) {
    $GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_enable'] = str_replace(',subscription_price,subscription_paymentMethods,subscription_paymentProvider,subscription_freeEmailDomains', '', $GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['subscription_enable']);
}
