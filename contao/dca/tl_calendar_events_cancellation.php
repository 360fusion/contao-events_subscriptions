<?php

use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

/*
 * Bookings the booker cancelled themselves, for the reports. Holds no personal data.
 */
$GLOBALS['TL_DCA']['tl_calendar_events_cancellation'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'closed' => true,
        'notEditable' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],
    'fields' => [
        'id' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true]],
        'pid' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0]],
        'tstamp' => ['sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0]],
        'places' => ['sql' => ['type' => Types::SMALLINT, 'unsigned' => true, 'default' => 1]],
    ],
];
