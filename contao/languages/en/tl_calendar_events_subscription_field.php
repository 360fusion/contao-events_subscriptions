<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['label'] = ['Label', 'The label shown on the registration form, in the export and in the backend.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['fieldKey'] = ['Field key', 'Machine name used for the notification token ##subscription_field_KEY## and to store the answers. Leave empty to generate it from the label. Changing it later hides the answers already given.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['type'] = ['Field type', 'Please choose the field type.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['options'] = ['Choices', 'One choice per line. The choice is stored and exported as entered.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['mandatory'] = ['Required by default', 'Make the field required unless a calendar or event says otherwise.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['disabled'] = ['Disable', 'Hide the field from registration forms and field pickers. Answers already given are kept and still exported.'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['field_legend'] = 'Field';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['config_legend'] = 'Settings';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['text'] = 'Text';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['textarea'] = 'Textarea';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['email'] = 'E-mail address';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['number'] = 'Number';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['select'] = 'Select menu';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['radio'] = 'Radio buttons';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['types']['checkbox'] = 'Checkbox (yes/no)';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription_field']['new'] = ['New field', 'Create a new registration field'];
