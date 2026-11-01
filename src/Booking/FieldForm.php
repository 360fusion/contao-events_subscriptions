<?php

namespace Codefog\EventsSubscriptionsBundle\Booking;

/**
 * The registration fields as a plain form, for changing the answers of a booking.
 * A field is array{key, label, type, options, mandatory} as returned by RegistrationFields::resolve().
 */
final class FieldForm
{
    /**
     * @param list<array>          $fields
     * @param array<string, mixed> $values Current answers by field key
     * @param array<string, string> $errors Error message by field key
     */
    public static function render(array $fields, array $values, array $errors): string
    {
        $html = '';

        foreach ($fields as $field) {
            $key = $field['key'];
            $name = 'field_'.$key;
            $value = (string) ($values[$key] ?? '');
            $label = Page::e($field['label']).($field['mandatory'] ? ' *' : '');
            $input = match ($field['type']) {
                'textarea' => '<textarea id="'.$name.'" name="'.$name.'" maxlength="2000">'.Page::e($value).'</textarea>',
                'select' => '<select id="'.$name.'" name="'.$name.'"><option value=""></option>'.self::options($field['options'], $value).'</select>',
                'radio' => self::radios($name, $field['options'], $value),
                'checkbox' => '<label class="inline"><input type="checkbox" name="'.$name.'" value="1"'.('' !== $value && '0' !== $value ? ' checked' : '').'> '.Page::e($field['label']).'</label>',
                'number' => '<input type="number" id="'.$name.'" name="'.$name.'" value="'.Page::e($value).'">',
                'email' => '<input type="email" id="'.$name.'" name="'.$name.'" maxlength="255" value="'.Page::e($value).'">',
                default => '<input type="text" id="'.$name.'" name="'.$name.'" maxlength="255" value="'.Page::e($value).'">',
            };

            $html .= ('checkbox' === $field['type'] ? '' : '<label for="'.$name.'">'.$label.'</label>').$input;

            if (isset($errors[$key])) {
                $html .= '<p class="error">'.Page::e($errors[$key]).'</p>';
            }
        }

        return $html;
    }

    /**
     * @param list<array>          $fields
     * @param array<string, mixed> $post
     *
     * @return array{0: array<string, string>, 1: array<string, string>} The answers by field key, and the errors by field key
     */
    public static function read(array $fields, array $post): array
    {
        $values = $errors = [];

        foreach ($fields as $field) {
            $raw = $post['field_'.$field['key']] ?? '';
            $value = \is_string($raw) ? trim($raw) : '';

            if ('checkbox' === $field['type']) {
                $value = $value ? '1' : '';
            }

            $values[$field['key']] = $value;

            if ('' === $value) {
                if ($field['mandatory'] && 'checkbox' !== $field['type']) {
                    $errors[$field['key']] = Page::t('required', $field['label']);
                }

                continue;
            }

            $valid = match ($field['type']) {
                'select', 'radio' => \in_array($value, $field['options'], true),
                'email' => false !== filter_var($value, FILTER_VALIDATE_EMAIL),
                'number' => 1 === preg_match('/^-?\d+([.,]\d+)?$/', $value),
                default => mb_strlen($value) <= ('textarea' === $field['type'] ? 2000 : 255),
            };

            if (!$valid) {
                $errors[$field['key']] = Page::t('invalid', $field['label']);
            }
        }

        return [$values, $errors];
    }

    private static function options(array $options, string $selected): string
    {
        $html = '';

        foreach ($options as $option) {
            $html .= '<option value="'.Page::e($option).'"'.($option === $selected ? ' selected' : '').'>'.Page::e($option).'</option>';
        }

        return $html;
    }

    private static function radios(string $name, array $options, string $selected): string
    {
        $html = '';

        foreach ($options as $option) {
            $html .= '<label class="inline"><input type="radio" name="'.$name.'" value="'.Page::e($option).'"'.($option === $selected ? ' checked' : '').'> '.Page::e($option).'</label>';
        }

        return $html;
    }
}
