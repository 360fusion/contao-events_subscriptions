<?php

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\DataContainer\EventEmailContainer;
use Contao\DataContainer;
use Contao\Input;

/**
 * Opened from Events › E-mails, the notifications show as a simple list of the event e-mails with one edit form.
 * Runs after the Notification Center's own DCA callbacks (priority -200). The Notification Center itself is unchanged.
 */
class EventEmailsListener
{
    public function __invoke(string $table): void
    {
        if ('tl_nc_notification' !== $table || 'calendar' !== Input::get('do')) {
            return;
        }

        $container = EventEmailContainer::class;
        $dca = &$GLOBALS['TL_DCA']['tl_nc_notification'];

        // Keep the child tables, so deleting an e-mail deletes its content too; no "edit child elements" button
        $dca['config']['switchToEdit'] = false;
        $dca['config']['onload_callback'][] = [$container, 'onLoad'];

        $dca['list']['sorting'] = [
            'mode' => DataContainer::MODE_SORTED,
            // Grouped by when the e-mail is sent, the headings use the readable type names (reference)
            'fields' => ['type', 'title'],
            'flag' => DataContainer::SORT_ASC,
            'panelLayout' => 'search,limit',
            'defaultSearchField' => 'title',
            'filter' => [['type LIKE ?', 'events_subscription%']],
        ];
        $dca['list']['label'] = ['fields' => ['title'], 'label_callback' => [$container, 'generateLabel']];
        // Contao has already expanded the default operations at this point: keep edit, copy and delete, no children
        $operations = $dca['list']['operations'] ?? [];
        $dca['list']['operations'] = array_filter([
            'edit' => $operations['edit'] ?? null,
            'test' => [
                'href' => 'key=event_emailTest',
                'icon' => 'mgroup.svg',
                'primary' => true,
                'attributes' => 'data-action="contao--scroll-offset#store"',
            ],
            'copy' => $operations['copy'] ?? null,
            'delete' => $operations['delete'] ?? null,
        ]);

        $dca['palettes'] = ['__selector__' => [], 'default' => '{title_legend},title,type'];
        $dca['fields']['type']['options_callback'] = [$container, 'getTypeOptions'];
        $dca['fields']['type']['reference'] = &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTypes'];
        $dca['fields']['type']['label'] = &$GLOBALS['TL_LANG']['tl_nc_notification']['emailType'];
        $dca['fields']['title']['label'] = &$GLOBALS['TL_LANG']['tl_nc_notification']['emailTitle'];

        // The content of the e-mail, stored in its Notification Center message and language
        $virtual = [
            'emailSubject' => ['inputType' => 'text', 'eval' => ['mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'long clr'], 'callbacks' => ['loadSubject', 'saveSubject']],
            'emailFormat' => ['inputType' => 'select', 'options' => ['text', 'html'], 'reference' => &$GLOBALS['TL_LANG']['tl_nc_notification']['emailFormatRef'], 'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'], 'callbacks' => ['loadFormat', 'saveFormat']],
            'emailText' => ['inputType' => 'textarea', 'eval' => ['mandatory' => true, 'decodeEntities' => true, 'style' => 'min-height:320px', 'tl_class' => 'clr'], 'callbacks' => ['loadText', 'saveText']],
            'emailHtml' => ['inputType' => 'textarea', 'eval' => ['mandatory' => true, 'rte' => 'tinyMCE', 'allowHtml' => true, 'decodeEntities' => true, 'tl_class' => 'clr'], 'callbacks' => ['loadHtml', 'saveHtml']],
            'emailSenderName' => ['inputType' => 'text', 'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'], 'callbacks' => ['loadSenderName', 'saveSenderName']],
            'emailSenderAddress' => ['inputType' => 'text', 'eval' => ['rgxp' => 'email', 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'], 'callbacks' => ['loadSenderAddress', 'saveSenderAddress']],
            'emailEnabled' => ['inputType' => 'checkbox', 'eval' => ['tl_class' => 'clr'], 'callbacks' => ['loadEnabled', 'saveEnabled']],
        ];

        foreach ($virtual as $name => $field) {
            [$load, $save] = $field['callbacks'];
            unset($field['callbacks']);

            // Stored in the Notification Center tables, not in tl_nc_notification
            $field['eval']['doNotSaveEmpty'] = true;

            $dca['fields'][$name] = $field + [
                'label' => &$GLOBALS['TL_LANG']['tl_nc_notification'][$name],
                'load_callback' => [[$container, $load]],
                'save_callback' => [[$container, $save]],
            ];
        }

        // "E-mail" instead of "notification" in this view only
        $GLOBALS['TL_LANG']['tl_nc_notification']['new'] = $GLOBALS['TL_LANG']['tl_nc_notification']['emailNew'];
        $GLOBALS['TL_LANG']['tl_nc_notification']['edit'] = $GLOBALS['TL_LANG']['tl_nc_notification']['emailEdit'];

        $dca['fields']['emailTags'] = [
            'label' => &$GLOBALS['TL_LANG']['tl_nc_notification']['emailTags'],
            'input_field_callback' => [$container, 'renderTags'],
        ];
    }
}
