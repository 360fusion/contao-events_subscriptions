<?php

use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

/*
 * The payments ledger. A payment stays when its booking is cancelled, so it can still be refunded.
 */
\Contao\System::loadLanguageFile('tl_calendar_events');

$GLOBALS['TL_DCA']['tl_calendar_events_payment'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'closed' => true,
        'notEditable' => true,
        'notCopyable' => true,
        'notDeletable' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'subscription' => 'index',
                'provider,reference' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTABLE,
            'fields' => ['dateCreated DESC'],
            'flag' => DataContainer::SORT_MONTH_DESC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['dateCreated', 'name', 'eventTitle', 'amount', 'status'],
            'showColumns' => true,
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'generatePaymentLabel'],
        ],
        'operations' => [
            'refund' => [
                'href' => 'key=payment_refund',
                'icon' => 'undo.svg',
                'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'refundButton'],
            ],
            'markPaid' => [
                'href' => 'key=payment_markPaid',
                'icon' => 'ok.svg',
                'button_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'markPaidButton'],
            ],
            'show',
        ],
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'dateCreated' => [
            'sorting' => true,
            'flag' => DataContainer::SORT_MONTH_DESC,
            'eval' => ['rgxp' => 'datim'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'subscription' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'event' => [
            'filter' => true,
            'foreignKey' => 'tl_calendar_events.title',
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'eventTitle' => [
            'search' => true,
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'name' => [
            'search' => true,
            'sorting' => true,
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'email' => [
            'search' => true,
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'method' => [
            'filter' => true,
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethodsRef'],
            'sql' => ['type' => Types::STRING, 'length' => 16, 'default' => ''],
        ],
        'status' => [
            'filter' => true,
            'reference' => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'],
            'sql' => ['type' => Types::STRING, 'length' => 16, 'default' => ''],
        ],
        'amount' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'currency' => [
            'sql' => ['type' => Types::STRING, 'length' => 3, 'default' => ''],
        ],
        'provider' => [
            'filter' => true,
            'foreignKey' => 'tl_calendar_events_payment_provider.title',
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'reference' => [
            'search' => true,
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'purchaseOrder' => [
            'search' => true,
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'paidAt' => [
            'eval' => ['rgxp' => 'datim'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'refundedAt' => [
            'eval' => ['rgxp' => 'datim'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
    ],
];

// Back end styles (the filter panel scrolls when it is taller than the window)
$GLOBALS['TL_DCA']['tl_calendar_events_payment']['config']['onload_callback'][] = ['Codefog\\EventsSubscriptionsBundle\\DataContainer\\CalendarContainer', 'addBackendStyles'];
