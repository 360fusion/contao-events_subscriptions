<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\RegistrationFields;
use Contao\Database;

class RegistrationFieldsListener
{
    /**
     * Expose every registration field as a virtual field of the subscription table, stored in "registrationFields".
     */
    public function onLoadDataContainer(string $table): void
    {
        if ('tl_calendar_events_subscription' !== $table) {
            return;
        }

        // The field table does not exist yet during installation or the first database update
        try {
            if (!Database::getInstance()->tableExists('tl_calendar_events_subscription_field')) {
                return;
            }
        } catch (\Exception) {
            return;
        }

        $registrationFields = RegistrationFields::fromDatabase();
        $names = [];

        foreach ($registrationFields->allFields() as $field) {
            $config = $registrationFields->formFieldConfig($field);
            $config['label'] = [$field['label'], ''];
            $config['targetColumn'] = 'registrationFields';
            $config['eval']['tl_class'] = 'checkbox' === $field['type'] ? 'w50 m12' : 'w50';

            // A single yes/no checkbox in the back end
            if ('checkbox' === $field['type']) {
                unset($config['options']);
            }

            $name = $registrationFields->virtualFieldName($field['key']);
            $GLOBALS['TL_DCA'][$table]['fields'][$name] = $config;
            $names[] = $name;
        }

        if ($names) {
            $GLOBALS['TL_DCA'][$table]['palettes']['guest'] .= ';{registration_fields_legend},'.implode(',', $names);
        }
    }
}
