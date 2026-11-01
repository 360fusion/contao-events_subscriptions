<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Contao\Database;
use Contao\DataContainer;

class SubscriptionFieldContainer
{
    private const MAX_LENGTH = 64;

    /**
     * Generate the field key from the label if it was left empty, otherwise normalize it.
     */
    public function saveFieldKey($value, DataContainer $dc): string
    {
        if ('' !== trim((string) $value)) {
            return $this->slug((string) $value);
        }

        $existingKeys = Database::getInstance()
            ->prepare('SELECT fieldKey FROM tl_calendar_events_subscription_field WHERE id!=?')
            ->execute($dc->id)
            ->fetchEach('fieldKey');

        return $this->generateKey((string) $dc->activeRecord->label, $existingKeys);
    }

    /**
     * Generate a unique machine name (lowercase ASCII and underscores) from a label.
     */
    public function generateKey(string $label, array $existingKeys): string
    {
        $base = $this->slug($label);
        $key = $base;

        for ($i = 2; \in_array($key, $existingKeys, true); ++$i) {
            $suffix = '_'.$i;
            $key = rtrim(substr($base, 0, self::MAX_LENGTH - \strlen($suffix)), '_').$suffix;
        }

        return $key;
    }

    private function slug(string $value): string
    {
        $slug = trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($value)), '_');

        return '' === $slug ? 'field' : rtrim(substr($slug, 0, self::MAX_LENGTH), '_');
    }
}
