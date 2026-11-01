<?php

use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

/*
 * Events › Trainers: the people who run events. An event or course has one chosen from here, or an e-mail address typed in.
 */
$GLOBALS['TL_DCA']['tl_calendar_events_trainer'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'ondelete_callback' => [['Codefog\EventsSubscriptionsBundle\DataContainer\TrainerContainer', 'onDelete']],
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
            'fields' => ['name'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['name'],
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\TrainerContainer', 'generateLabel'],
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
        'default' => '{trainer_legend},name,email;{publish_legend},published',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
        ],
        'name' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
        ],
        'email' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'email', 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
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
