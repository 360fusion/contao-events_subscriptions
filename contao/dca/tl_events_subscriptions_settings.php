<?php

use Contao\DC_File;

/*
 * Events › Setup › Booking settings. Stored in the system configuration, like System › Settings.
 */
$GLOBALS['TL_DCA']['tl_events_subscriptions_settings'] = [
    'config' => [
        'dataContainer' => DC_File::class,
        'closed' => true,
    ],

    'palettes' => [
        '__selector__' => ['eventsSubscriptionsRetention'],
        'default' => '{defaults_legend},eventsSubscriptionsTemplateCalendar;{retention_legend},eventsSubscriptionsRetention',
    ],

    'subpalettes' => [
        'eventsSubscriptionsRetention' => 'eventsSubscriptionsRetentionMonths,eventsSubscriptionsRetentionAction',
    ],

    'fields' => [
        'eventsSubscriptionsTemplateCalendar' => [
            'inputType' => 'select',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'getCalendarOptions'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsRetention' => [
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
        ],
        'eventsSubscriptionsRetentionMonths' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'natural', 'minval' => 1, 'maxlength' => 4, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsRetentionAction' => [
            'inputType' => 'select',
            'options' => ['anonymise', 'delete'],
            'reference' => &$GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['retentionActions'],
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
        ],
    ],
];
