<?php

use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

$GLOBALS['TL_DCA']['tl_calendar_events_payment_provider'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['title'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;limit',
        ],
        'global_operations' => [
            'back' => ['label' => &$GLOBALS['TL_LANG']['MSC']['backBT'], 'href' => '', 'class' => 'header_back', 'primary' => true, 'button_callback' => ['Codefog\\EventsSubscriptionsBundle\\Backend\\BackLink', 'toPayments']],
            'new',
            'all',
        ],
        'label' => [
            'fields' => ['title'],
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'generateProviderLabel'],
        ],
        'operations' => [
            'edit',
            'delete',
            'toggle',
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['type'],
        'default' => '{title_legend},title,type;{publish_legend},published',
        'stripe' => '{title_legend},title,type;{config_legend},currency,secretKey;{publish_legend},published',
        'paypal' => '{title_legend},title,type;{config_legend},live,currency,clientId,clientSecret;{publish_legend},published',
        'mollie' => '{title_legend},title,type;{config_legend},currency,apiKey;{publish_legend},published',
        'govukpay' => '{title_legend},title,type;{config_legend},apiKey;{publish_legend},published',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            // A new provider opened from a tile of the Payments page starts with its kind's name
            'default' => static fn () => $GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'][\Contao\Input::get('type')] ?? '',
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'type' => [
            'default' => static fn () => isset(\Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory::TYPES[\Contao\Input::get('type')]) ? \Contao\Input::get('type') : '',
            'inputType' => 'select',
            'options' => array_keys(\Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory::TYPES),
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'],
            'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => ''],
        ],
        // Switched off, a provider is no longer offered on new bookings, but keeps its settings and its payments can still be refunded
        'published' => [
            'filter' => true,
            'toggle' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'tl_class' => 'clr'],
            'save_callback' => [['Codefog\\EventsSubscriptionsBundle\\DataContainer\\PaymentContainer', 'validateActive']],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false],
        ],
        'live' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false],
        ],
        'currency' => [
            'default' => 'GBP',
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'minlength' => 3, 'maxlength' => 3, 'rgxp' => 'alpha', 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 3, 'default' => 'GBP'],
        ],
        // Credentials: only for admins (or users explicitly allowed to edit them), never shown again in full
        'secretKey' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'hideInput' => true, 'decodeEntities' => true, 'tl_class' => 'clr long'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'clientId' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'tl_class' => 'clr long'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'clientSecret' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'hideInput' => true, 'decodeEntities' => true, 'tl_class' => 'clr long'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'apiKey' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'hideInput' => true, 'decodeEntities' => true, 'tl_class' => 'clr long'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
    ],
];
