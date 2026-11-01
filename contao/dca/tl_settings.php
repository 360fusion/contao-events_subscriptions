<?php

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{events_subscriptions_legend},eventsSubscriptionsCalendarGroups';

$GLOBALS['TL_DCA']['tl_settings']['fields']['eventsSubscriptionsCalendarGroups'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
];
