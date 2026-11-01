<?php

use Contao\DC_File;

/*
 * Events › Settings › Booking settings, in five pages (General, Payments, E-mails, Certificates, Before and after events). Stored in the system configuration, like System › Settings.
 */
$GLOBALS['TL_DCA']['tl_events_subscriptions_settings'] = [
    'config' => [
        'dataContainer' => DC_File::class,
        'closed' => true,
        'onload_callback' => [
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'selectPalette'],
            ['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'showDefaultTexts'],
        ],
        'onsubmit_callback' => [['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'applySender']],
    ],

    // One page for each area of Events › Settings › Booking settings, chosen by the module (see SettingsContainer::selectPalette())
    'palettes' => [
        '__selector__' => ['eventsSubscriptionsRetention', 'eventsSubscriptionsPayments'],
        'default' => '{features_legend},eventsSubscriptionsAttendance,eventsSubscriptionsCalendarGroups,eventsSubscriptionsOneBookingPerCourse,eventsSubscriptionsVenues,eventsSubscriptionsTrainers;{defaults_legend},eventsSubscriptionsTemplateCalendar;{reports_legend},eventsSubscriptionsNoShowLimit;{wording_legend},eventsSubscriptionsTextIntro,eventsSubscriptionsTextClosed,eventsSubscriptionsTextFull,eventsSubscriptionsTextWaitingList,eventsSubscriptionsTextConfirmation;{retention_legend},eventsSubscriptionsRetention',
        'payments' => '{payments_legend},eventsSubscriptionsPayments',
        'emails' => '{emails_legend},eventsSubscriptionsSenderName,eventsSubscriptionsSenderAddress,eventsSubscriptionsSignature',
        'certificates' => '{certificate_legend},eventsSubscriptionsCertificate,eventsSubscriptionsCertificateOrganisation,eventsSubscriptionsCertificateSignedBy',
        'mailings' => '{mailings_legend},eventsSubscriptionsTrainerNotification,eventsSubscriptionsTrainerHour,eventsSubscriptionsFollowUpNotification',
    ],

    'subpalettes' => [
        'eventsSubscriptionsRetention' => 'eventsSubscriptionsRetentionMonths,eventsSubscriptionsRetentionAction',
        'eventsSubscriptionsPayments' => 'eventsSubscriptionsPaymentHold,eventsSubscriptionsInvoiceText',
    ],

    'fields' => [
        'eventsSubscriptionsPayments' => [
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
        ],
        'eventsSubscriptionsPaymentHold' => [
            'inputType' => 'text',
            'load_callback' => [static fn ($value) => '' === (string) $value ? '30' : $value],
            'eval' => ['rgxp' => 'natural', 'minval' => 30, 'maxval' => 1440, 'maxlength' => 4, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsInvoiceText' => [
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:90px'],
        ],
        'eventsSubscriptionsAttendance' => [
            'inputType' => 'checkbox',
            'load_callback' => [static fn ($value) => null === $value ? '1' : $value], // on unless switched off
            'eval' => ['tl_class' => 'w50'],
        ],
        'eventsSubscriptionsCalendarGroups' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'eventsSubscriptionsTrainers' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'eventsSubscriptionsVenues' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'eventsSubscriptionsOneBookingPerCourse' => [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'eventsSubscriptionsTrainerNotification' => [
            'inputType' => 'select',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'getTrainerNotifications'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsTrainerHour' => [
            'inputType' => 'text',
            'load_callback' => [static fn ($value) => '' === (string) $value ? '16' : $value],
            'eval' => ['rgxp' => 'natural', 'minval' => 0, 'maxval' => 23, 'maxlength' => 2, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsFollowUpNotification' => [
            'inputType' => 'select',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'getFollowUpNotifications'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsCertificate' => [
            'inputType' => 'checkbox',
            'load_callback' => [static fn ($value) => null === $value ? '1' : $value], // on unless switched off
            'eval' => ['tl_class' => 'clr'],
        ],
        'eventsSubscriptionsCertificateOrganisation' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsCertificateSignedBy' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsNoShowLimit' => [
            'inputType' => 'text',
            'load_callback' => [static fn ($value) => '' === (string) $value ? '3' : $value],
            'eval' => ['rgxp' => 'natural', 'minval' => 1, 'maxlength' => 3, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsSenderName' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsSenderAddress' => [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'email', 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsSignature' => [
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:110px'],
        ],
        'eventsSubscriptionsTemplateCalendar' => [
            'inputType' => 'select',
            'options_callback' => ['Codefog\EventsSubscriptionsBundle\DataContainer\SettingsContainer', 'getCalendarOptions'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        ],
        'eventsSubscriptionsTextIntro' => ['inputType' => 'textarea', 'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:56px']],
        'eventsSubscriptionsTextClosed' => ['inputType' => 'textarea', 'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:56px']],
        'eventsSubscriptionsTextFull' => ['inputType' => 'textarea', 'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:56px']],
        'eventsSubscriptionsTextWaitingList' => ['inputType' => 'textarea', 'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:56px']],
        'eventsSubscriptionsTextConfirmation' => ['inputType' => 'textarea', 'eval' => ['decodeEntities' => true, 'tl_class' => 'clr', 'style' => 'height:56px']],
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

// The system configuration cannot store line breaks: keep them as "\n" in the multi-line settings
foreach (['eventsSubscriptionsInvoiceText', 'eventsSubscriptionsSignature', 'eventsSubscriptionsTextIntro', 'eventsSubscriptionsTextClosed', 'eventsSubscriptionsTextFull', 'eventsSubscriptionsTextWaitingList', 'eventsSubscriptionsTextConfirmation'] as $field) {
    $GLOBALS['TL_DCA']['tl_events_subscriptions_settings']['fields'][$field]['load_callback'][] = static fn ($value) => str_replace('\\n', "\n", (string) $value);
    $GLOBALS['TL_DCA']['tl_events_subscriptions_settings']['fields'][$field]['save_callback'][] = static fn ($value) => str_replace(["\r\n", "\n"], '\\n', (string) $value);
}

unset($field);

