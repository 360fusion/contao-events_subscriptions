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
            'panelLayout' => 'limit',
        ],
        'label' => [
            'fields' => ['title'],
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\PaymentContainer', 'generateProviderLabel'],
        ],
        'operations' => [
            'edit',
            'delete',
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['type'],
        'default' => '{title_legend},title,type',
        'stripe' => '{title_legend},title,type;{config_legend},currency,secretKey',
        'paypal' => '{title_legend},title,type;{config_legend},live,currency,clientId,clientSecret',
        'mollie' => '{title_legend},title,type;{config_legend},currency,apiKey',
        'govukpay' => '{title_legend},title,type;{config_legend},apiKey',
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
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'type' => [
            'inputType' => 'select',
            'options' => array_keys(\Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory::TYPES),
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'],
            'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => ''],
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
