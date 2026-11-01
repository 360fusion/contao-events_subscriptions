<?php

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_calendar_group'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'ondelete_callback' => [
            ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarGroupContainer', 'releaseCalendars'],
        ],
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
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarGroupContainer', 'generateLabel'],
        ],
        'operations' => [
            'edit',
            'delete',
            'export' => [
                'href' => 'key=subscriptions_exportGroup',
                'icon' => 'theme_import.svg',
            ],
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,calendars',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        // Stored on the calendars (tl_calendar.calendarGroup), so each calendar belongs to one group
        'calendars' => [
            'inputType' => 'checkbox',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarGroupContainer', 'getCalendarOptions'],
            'eval' => ['multiple' => true, 'doNotSaveEmpty' => true, 'tl_class' => 'clr'],
            'load_callback' => [
                ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarGroupContainer', 'loadCalendars'],
            ],
            'save_callback' => [
                ['Codefog\EventsSubscriptionsBundle\DataContainer\CalendarGroupContainer', 'saveCalendars'],
            ],
        ],
    ],
];
