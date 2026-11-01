<?php

use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

/*
 * Events › Venues and locations: the places events are held. Choosing one on an event fills in the event's location and address.
 */
$GLOBALS['TL_DCA']['tl_calendar_events_venue'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'onsubmit_callback' => [['Codefog\EventsSubscriptionsBundle\DataContainer\VenueContainer', 'onSave']],
        'ondelete_callback' => [['Codefog\EventsSubscriptionsBundle\DataContainer\VenueContainer', 'onDelete']],
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
            'panelLayout' => 'filter;search,limit',
        ],
        'global_operations' => [
            'back' => ['label' => &$GLOBALS['TL_LANG']['MSC']['backBT'], 'href' => '', 'class' => 'header_back', 'primary' => true, 'button_callback' => ['Codefog\\EventsSubscriptionsBundle\\Backend\\BackLink', 'toSettings']],
            'new',
            'all',
        ],
        'label' => [
            'fields' => ['title'],
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\VenueContainer', 'generateLabel'],
        ],
        'operations' => [
            'edit',
            'copy',
            'delete',
            'toggle',
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{venue_legend},title,street,town,postcode;{info_legend},directions,accessibility,mapLink;{publish_legend},published',
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
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'street' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'town' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 128, 'default' => ''],
        ],
        'postcode' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 16, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 16, 'default' => ''],
        ],
        'directions' => [
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:100px'],
            'sql' => ['type' => Types::TEXT, 'notnull' => false],
        ],
        'accessibility' => [
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:100px'],
            'sql' => ['type' => Types::TEXT, 'notnull' => false],
        ],
        'mapLink' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'published' => [
            'filter' => true,
            'toggle' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => true],
        ],
    ],
];
