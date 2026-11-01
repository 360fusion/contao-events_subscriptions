<?php

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_calendar_event_group'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_calendar',
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'ondelete_callback' => [
            ['Codefog\EventsSubscriptionsBundle\DataContainer\EventGroupContainer', 'releaseEvents'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
                'alias' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['title'],
            'disableGrouping' => true,
            'headerFields' => ['title'],
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'label_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\EventGroupContainer', 'generateLabel'],
        ],
        'operations' => [
            'edit',
            'copy',
            'delete',
            'toggle',
            'export' => [
                'href' => 'key=subscriptions_exportEventGroup',
                'icon' => 'theme_import.svg',
            ],
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,alias;{description_legend},description;{publish_legend},published',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'pid' => [
            'foreignKey' => 'tl_calendar.title',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
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
        // Unique across all calendars, so "?group=<alias>" alone identifies the group
        'alias' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'save_callback' => [
                ['Codefog\EventsSubscriptionsBundle\DataContainer\EventGroupContainer', 'generateAlias'],
            ],
            'sql' => "varchar(255) BINARY NOT NULL default ''",
        ],
        'description' => [
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'basicEntities' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'text', 'notnull' => false],
        ],
        'published' => [
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
    ],
];

// Booking settings: the same overrides as an event, used by the events of the group that do not override them
\Contao\Controller::loadDataContainer('tl_calendar_events');
\Contao\System::loadLanguageFile('tl_calendar_events');

foreach ($GLOBALS['TL_DCA']['tl_calendar_events']['fields'] as $name => $field) {
    if (str_starts_with($name, 'subscription_')) {
        $GLOBALS['TL_DCA']['tl_calendar_event_group']['fields'][$name] = $field;

        // The override toggles have their own wording, the other fields use the event labels
        if (\in_array($name, ['subscription_customEmailDomains', 'subscription_customNotifications', 'subscription_customPayment', 'subscription_override'], true)) {
            $GLOBALS['TL_DCA']['tl_calendar_event_group']['fields'][$name]['label'] = &$GLOBALS['TL_LANG']['tl_calendar_event_group'][$name];
        } else {
            $GLOBALS['TL_DCA']['tl_calendar_event_group']['fields'][$name]['label'] = $GLOBALS['TL_LANG']['tl_calendar_events'][$name] ?? $field['label'] ?? null;
        }
    }
}

foreach ($GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes'] as $selector => $subpalette) {
    if (str_starts_with($selector, 'subscription_')) {
        $GLOBALS['TL_DCA']['tl_calendar_event_group']['palettes']['__selector__'][] = $selector;
        $GLOBALS['TL_DCA']['tl_calendar_event_group']['subpalettes'][$selector] = $subpalette;
    }
}

$overrides = array_intersect(array_filter(['subscription_customEmailDomains', 'subscription_customNotifications', \Codefog\EventsSubscriptionsBundle\Features::payments() ? 'subscription_customPayment' : null, 'subscription_override', \Codefog\EventsSubscriptionsBundle\Features::trainers() ? 'subscription_trainer' : null, 'subscription_trainerEmail']), array_keys($GLOBALS['TL_DCA']['tl_calendar_event_group']['fields']));

if ($overrides) {
    $GLOBALS['TL_DCA']['tl_calendar_event_group']['palettes']['default'] = str_replace('{publish_legend}', '{subscription_legend},'.implode(',', $overrides).';{publish_legend}', $GLOBALS['TL_DCA']['tl_calendar_event_group']['palettes']['default']);
}

unset($name, $field, $selector, $subpalette, $overrides);

