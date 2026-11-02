<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Doctrine\DBAL\Types\Types;

// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_list_subscribe'] = '{title_legend},name,headline,type;{config_legend},cal_allCalendars,cal_calendar,cal_filterByUrl,cal_noSpan,cal_format,cal_featured,cal_order,cal_readerModule,cal_limit,perPage,cal_ignoreDynamic,cal_hideRunning,cal_showParticipants;{redirect_legend:hide},jumpTo_subscribe,jumpTo_waitingList,jumpTo_unsubscribe;;{template_legend:hide},cal_template,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_reader_subscribe'] = '{title_legend},name,headline,type;{config_legend},cal_allCalendars,cal_calendar,cal_hideRunning,cal_keepCanonical,cal_showParticipants;{cal_overview_legend},overviewPage,customLabel;{redirect_legend:hide},jumpTo_subscribe,jumpTo_waitingList,jumpTo_unsubscribe;{template_legend:hide},cal_template,customTpl;{image_legend},imgSize;{protected_legend:hide},protected;{expert_legend:hide},cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_subscribe'] = '{title_legend},name,headline,type;{redirect_legend:hide},jumpTo_subscribe,jumpTo_waitingList,jumpTo_unsubscribe;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_subscriptions'] = '{title_legend},name,headline,type;{config_legend},cal_calendar,cal_noSpan,cal_format,cal_ignoreDynamic,cal_order,cal_readerModule,cal_limit,perPage,cal_showParticipants;{template_legend:hide},cal_template,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

PaletteManipulator::create()
    ->addField('cal_showParticipants', 'config_legend', PaletteManipulator::POSITION_APPEND)
    ->addField(['jumpTo_subscribe', 'jumpTo_waitingList', 'jumpTo_unsubscribe'], 'redirect_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('calendar', 'tl_module');

PaletteManipulator::create()
    ->addField('cal_filterByUrl', 'cal_calendar', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('eventlist', 'tl_module')
    ->applyToPalette('eventmenu', 'tl_module');

$GLOBALS['TL_DCA']['tl_module']['palettes']['event_navigation'] = '{title_legend},name,headline,type;{config_legend},cal_allCalendars,cal_calendar,jumpTo,cal_hideEmptyGroups,cal_showCounts;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID';

// The calendars are optional when "all calendars" is ticked
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = ['Codefog\EventsSubscriptionsBundle\DataContainer\ModuleContainer', 'adjustCalendarField'];

// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo_subscribe'] = [
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo_waitingList'] = [
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo_unsubscribe'] = [
    'inputType' => 'pageTree',
    'eval' => ['fieldType' => 'radio'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cal_showParticipants'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cal_allCalendars'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cal_filterByUrl'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cal_hideEmptyGroups'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['cal_showCounts'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => true],
];
