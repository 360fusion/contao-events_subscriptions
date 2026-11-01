<?php

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_calendar_events_subscription_field'] = [

    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'fieldKey' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['label'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;search,limit',
        ],
        'global_operations' => [
            'back' => ['label' => &$GLOBALS['TL_LANG']['MSC']['backBT'], 'href' => '', 'class' => 'header_back', 'primary' => true, 'button_callback' => ['Codefog\\EventsSubscriptionsBundle\\Backend\\BackLink', 'toSettings']],
            'new',
            'all',
        ],
        'label' => [
            'fields' => ['label', 'fieldKey', 'type'],
            'format' => '%s <span class="label-info">[%s, %s]</span>',
        ],
        'operations' => [
            'edit',
            'copy',
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['subscriptionFieldDeleteConfirm'] ?? null).'\'))return false"',
            ],
            'toggle',
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['type'],
        'default' => '{field_legend},label,fieldKey,type;{config_legend},mandatory,disabled',
        'select' => '{field_legend},label,fieldKey,type,options;{config_legend},mandatory,disabled',
        'radio' => '{field_legend},label,fieldKey,type,options;{config_legend},mandatory,disabled',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'label' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'fieldKey' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'unique' => true, 'doNotCopy' => true, 'tl_class' => 'w50'],
            'save_callback' => [
                ['Codefog\EventsSubscriptionsBundle\DataContainer\SubscriptionFieldContainer', 'saveFieldKey'],
            ],
            'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
        ],
        'type' => [
            'filter' => true,
            'inputType' => 'select',
            'options' => ['text', 'textarea', 'email', 'number', 'select', 'radio', 'checkbox'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types'],
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 16, 'default' => 'text'],
        ],
        'options' => [
            'inputType' => 'listWizard',
            'eval' => ['mandatory' => true, 'allowHtml' => false, 'tl_class' => 'clr'],
            'sql' => ['type' => 'blob', 'notnull' => false],
        ],
        'mandatory' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disabled' => [
            'reverseToggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
    ],
];
