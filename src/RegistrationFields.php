<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionFieldModel;
use Contao\StringUtil;

/**
 * Turns the editor-defined registration fields and a calendar/event selection
 * into form field configs, export columns and notification tokens.
 *
 * A field is represented as array{key: string, label: string, type: string, options: list<string>, mandatory: bool}.
 */
class RegistrationFields
{
    /**
     * @var array<int, array> Field rows indexed by ID, in definition order
     */
    private array $rows = [];

    /**
     * @param array<array> $fieldRows Rows of tl_calendar_events_subscription_field
     */
    public function __construct(array $fieldRows)
    {
        foreach ($fieldRows as $row) {
            $this->rows[(int) $row['id']] = $row;
        }
    }

    private static self|null $fromDatabase = null;

    public static function fromDatabase(): self
    {
        // The exporter asks every subscription for its columns, so load the fields once per request
        return self::$fromDatabase ??= new self(array_map(static fn (SubscriptionFieldModel $model) => $model->row(), SubscriptionFieldModel::findAllOrdered()));
    }

    /**
     * Resolve the "subscription_fields" selection of a calendar or event. Unknown and disabled fields are skipped.
     */
    public function resolve(array|string|null $selection): array
    {
        $fields = [];

        foreach (StringUtil::deserialize($selection, true) as $item) {
            $row = $this->rows[(int) ($item['field'] ?? 0)] ?? null;

            if (null === $row || $row['disabled'] || isset($fields[$row['fieldKey']])) {
                continue;
            }

            $field = $this->toField($row);
            $override = (string) ($item['mandatory'] ?? '');

            if ('' !== $override) {
                $field['mandatory'] = '1' === $override;
            }

            $fields[$row['fieldKey']] = $field;
        }

        return array_values($fields);
    }

    /**
     * All defined fields including disabled ones, in definition order, never mandatory.
     */
    public function allFields(): array
    {
        return array_values(array_map(fn (array $row) => ['mandatory' => false] + $this->toField($row), $this->rows));
    }

    /**
     * Get the Haste form field config of a field.
     */
    public function formFieldConfig(array $field): array
    {
        $config = [
            'label' => $field['label'],
            'inputType' => $field['type'],
            'eval' => ['mandatory' => $field['mandatory']],
        ];

        switch ($field['type']) {
            case 'text':
                $config['eval']['maxlength'] = 255;
                break;

            case 'email':
                $config['inputType'] = 'text';
                $config['eval']['rgxp'] = 'email';
                $config['eval']['maxlength'] = 255;
                break;

            case 'number':
                $config['inputType'] = 'text';
                $config['eval']['rgxp'] = 'digit';
                break;

            case 'select':
            case 'radio':
                $config['options'] = array_combine($field['options'], $field['options']);
                $config['eval']['includeBlankOption'] = 'select' === $field['type'];
                break;

            case 'checkbox':
                $config['options'] = [1 => $field['label']];
                break;
        }

        return $config;
    }

    public function virtualFieldName(string $key): string
    {
        return 'registrationField_'.$key;
    }

    /**
     * @return array<string, string> Token/column name => label for every defined field
     */
    public function exportColumns(): array
    {
        $columns = [];

        foreach ($this->allFields() as $field) {
            $columns['subscription_field_'.$field['key']] = $field['label'];
        }

        return $columns;
    }

    public function formatValue(array $field, mixed $value): string
    {
        if ('checkbox' === ($field['type'] ?? null)) {
            return $value ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'];
        }

        return (string) $value;
    }

    private function toField(array $row): array
    {
        return [
            'key' => $row['fieldKey'],
            'label' => $row['label'],
            'type' => $row['type'],
            'options' => array_values(array_filter(StringUtil::deserialize($row['options'] ?? null, true), static fn ($option) => '' !== (string) $option)),
            'mandatory' => (bool) $row['mandatory'],
        ];
    }
}
