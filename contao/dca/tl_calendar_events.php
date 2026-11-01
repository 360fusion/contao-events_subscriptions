<?php

use Contao\ArrayUtil;
use Doctrine\DBAL\Types\Types;

// Load the language files
\Contao\System::loadLanguageFile('tl_calendar');
\Contao\System::loadLanguageFile('tl_calendar_events_subscription');

// Config
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ctable'][] = 'tl_calendar_events_subscription';

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = [
    'Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer',
    'extendPalette',
];

if (($index = array_search(['tl_calendar_events', 'checkPermission'], $GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'])) !== false) {
    $GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][$index] = [
        'Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer',
        'checkPermission',
    ];
}

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = [
    'Codefog\EventsSubscriptionsBundle\DataContainer\VenueContainer',
    'lockLocation',
];
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onsubmit_callback'][] = [
    'Codefog\EventsSubscriptionsBundle\DataContainer\VenueContainer',
    'syncEvent',
];

// Global operations
ArrayUtil::arrayInsert($GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations'], 0, [
    'subscriptions_overview' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_overview'],
        'href' => 'key=subscriptions_overview',
        'icon' => 'mgroup.svg',
        'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getSubscriptionsOverviewButton'],
    ],
    'subscriptions_export' => [
        'label' => &$GLOBALS['TL_LANG']['tl_calendar']['subscriptions_export'],
        'href' => 'key=subscriptions_exportCalendar',
        'icon' => 'theme_import.svg',
        'attributes' => 'onclick="Backend.getScrollOffset()"',
        'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getSubscriptionsExportButton'],
    ],
]);

// List operations
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['sendNotifications'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['sendNotifications'],
    'href' => 'key=subscriptions_notification',
    'icon' => 'mover.svg',
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getNotificationsButton'],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['subscriptionsRegister'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptionsRegister'],
    'href' => 'key=subscriptions_register',
    'icon' => 'tablewizard.svg',
    'attributes' => 'target="_blank"',
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getNotificationsButton'],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['subscriptions'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'],
    'href' => 'table=tl_calendar_events_subscription',
    'icon' => 'mgroup.svg',
    'primary' => true, // shown next to edit, not in the "…" menu
    'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getSubscriptionsButton'],
];

// Palettes
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_override';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_customEmailDomains';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_waitingList';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_memberGroupsLimit';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_numberOfParticipants';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_customEmailDomains'] = 'subscription_emailDomains';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_override'] = 'subscription_types,subscription_memberGroupsLimit,subscription_maximum,subscription_uniqueEmails,subscription_subscribeEndTime,subscription_unsubscribeEndTime,subscription_numberOfParticipants,subscription_fields,subscription_waitingList,subscription_disableReminders';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_customNotifications';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_customNotifications'] = 'subscription_subscribeNotification,subscription_unsubscribeNotification,subscription_listUpdateNotification,subscription_notification';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_waitingList'] = 'subscription_waitingListLimit';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'subscription_customPayment';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_customPayment'] = 'subscription_price,subscription_paymentMethods,subscription_paymentProvider,subscription_freeEmailDomains';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_memberGroupsLimit'] = 'subscription_memberGroups';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['subscription_numberOfParticipants'] = 'subscription_numberOfParticipantsLimit';

// Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_override'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types'] = [
    'inputType' => 'checkbox',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getTypes'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'],
    'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_fields'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields'],
    'inputType' => 'rowWizard',
    'fields' => [
        'field' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_field'],
            'inputType' => 'select',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventsContainer', 'getRegistrationFieldOptions'],
            'eval' => ['includeBlankOption' => true],
        ],
        'mandatory' => [
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatory'],
            'inputType' => 'select',
            'options' => ['1', '0'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef'],
            'eval' => ['includeBlankOption' => true, 'blankOptionLabel' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_fields_mandatoryRef']['']],
        ],
    ],
    'eval' => ['sortable' => true, 'actions' => ['copy', 'delete'], 'min' => 1, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_customEmailDomains'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customEmailDomains'],
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_customNotifications'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customNotifications'],
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

// Payment: price (stored in pence), how people can pay, the provider and the domains that book free
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_customPayment'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_customPayment'],
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_price'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_price'],
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'minval' => 0, 'maxlength' => 10, 'tl_class' => 'w50'],
    'load_callback' => [['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'loadPrice']],
    'save_callback' => [['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'savePrice']],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_paymentMethods'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethods'],
    'inputType' => 'checkbox',
    'options' => ['card', 'invoice'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethodsRef'],
    'eval' => ['multiple' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_paymentProvider'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentProvider'],
    'inputType' => 'select',
    'foreignKey' => 'tl_calendar_events_payment_provider.title',
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50 clr'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_freeEmailDomains'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_freeEmailDomains'],
    'inputType' => 'listWizard',
    'eval' => ['multiple' => true, 'csv' => ',', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::TEXT, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_emailDomains'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_emailDomains'],
    'inputType' => 'listWizard',
    'eval' => ['multiple' => true, 'csv' => ',', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::TEXT, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_uniqueEmails'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_uniqueEmails'],
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

// Venues and locations: the column always exists, the choice is shown once venues are switched on
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['venue'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['venue'],
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\VenueContainer', 'getOptions'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

if (\Codefog\EventsSubscriptionsBundle\Features::venues()) {
    foreach (['default', 'internal', 'article', 'external'] as $palette) {
        \Contao\CoreBundle\DataContainer\PaletteManipulator::create()
            ->addField('venue', 'location', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_BEFORE)
            ->applyToPalette($palette, 'tl_calendar_events');
    }

    unset($palette);
}

// Trainers: the choice is shown once the Trainers list is switched on; a typed address (below) is used when none is chosen
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_trainer'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_trainer'],
    'inputType' => 'select',
    'options_callback' => ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\TrainerContainer', 'getOptions'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_trainerEmail'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_trainerEmail'],
    'inputType' => 'text',
    'eval' => ['rgxp' => 'email', 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
];

// When the trainer pack was sent (see Mailings)
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['trainerPackSent'] = [
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_disableReminders'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_disableReminders'],
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeNotification'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeNotification'],
    'inputType' => 'select',
    'options_callback' => ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'getSubscribeNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeNotification'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeNotification'],
    'inputType' => 'select',
    'options_callback' => ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'getUnsubscribeNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_listUpdateNotification'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_listUpdateNotification'],
    'inputType' => 'select',
    'options_callback' => ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'getListUpdateNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_notification'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_notification'],
    'inputType' => 'select',
    'options_callback' => ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'getNotifications'],
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroupsLimit'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_memberGroups'] = [
    'inputType' => 'checkbox',
    'flag' => 1,
    'foreignKey' => 'tl_member_group.name',
    'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BLOB, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_maximum'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_subscribeEndTime'] = [
    'inputType' => 'timePeriod',
    'options' => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'],
    'eval' => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_unsubscribeEndTime'] = [
    'inputType' => 'timePeriod',
    'options' => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'],
    'eval' => ['rgxp' => 'natural', 'minval' => 1, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipants'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_numberOfParticipantsLimit'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingList'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_waitingListLimit'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'clr'],
    'sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_lastNotificationSent'] = [
    'eval' => ['rgxp' => 'datim', 'doNotCopy' => true],
    'sql' => ['type' => Types::STRING, 'length' => 10, 'default' => ''],
];

// The subscription fields are shared with tl_calendar by reference. Contao 5.7 may load tl_calendar while
// tl_calendar_events is still loading, and its default labels would then point to tl_calendar's language file.
foreach ($GLOBALS['TL_DCA']['tl_calendar_events']['fields'] as $name => &$field) {
    if (str_starts_with($name, 'subscription_') && !array_key_exists('label', $field)) {
        $field['label'] = &$GLOBALS['TL_LANG']['tl_calendar_events'][$name];
    }
}

unset($field);

// Event group (a course within the calendar)
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField('eventGroup', 'author', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_calendar_events')
    ->applyToPalette('internal', 'tl_calendar_events')
    ->applyToPalette('article', 'tl_calendar_events')
    ->applyToPalette('external', 'tl_calendar_events');

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['eventGroup'] = [
    'filter' => true,
    'inputType' => 'select',
    'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventGroupContainer', 'getEventGroupOptions'],
    'foreignKey' => 'tl_calendar_event_group.title',
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
];

// Back end styles (the filter panel scrolls when it is taller than the window)
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'addBackendStyles'];
