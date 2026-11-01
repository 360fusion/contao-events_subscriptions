<?php

use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

/*
 * What attendees said after the event. No name or e-mail address is stored: "subscription" only stops a second answer.
 */
$GLOBALS['TL_DCA']['tl_calendar_events_feedback'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'closed' => true,
        'notEditable' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
                'subscription' => 'index',
            ],
        ],
    ],
    'fields' => [
        'id' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true]],
        'pid' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0]],
        'tstamp' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0]],
        'subscription' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0]],
        'rating' => ['sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 0]],
        'comment' => ['sql' => ['type' => Types::TEXT, 'notnull' => false]],
    ],
];
