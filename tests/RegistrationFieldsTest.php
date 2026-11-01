<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\RegistrationFields;
use PHPUnit\Framework\TestCase;

class RegistrationFieldsTest extends TestCase
{
    private const ROWS = [
        ['id' => 1, 'label' => 'Ward / Area', 'fieldKey' => 'ward_area', 'type' => 'text', 'options' => null, 'mandatory' => 1, 'disabled' => 0],
        ['id' => 2, 'label' => 'Diet', 'fieldKey' => 'diet', 'type' => 'select', 'options' => 'a:2:{i:0;s:4:"None";i:1;s:5:"Vegan";}', 'mandatory' => 0, 'disabled' => 0],
        ['id' => 3, 'label' => 'Old', 'fieldKey' => 'old', 'type' => 'text', 'options' => null, 'mandatory' => 0, 'disabled' => 1],
        ['id' => 4, 'label' => 'Approved', 'fieldKey' => 'approved', 'type' => 'checkbox', 'options' => null, 'mandatory' => 0, 'disabled' => 0],
    ];

    protected function setUp(): void
    {
        $GLOBALS['TL_LANG']['MSC']['yes'] = 'Yes';
        $GLOBALS['TL_LANG']['MSC']['no'] = 'No';
    }

    public function testResolveOrderOverridesAndSkips(): void
    {
        $r = (new RegistrationFields(self::ROWS))->resolve([
            ['field' => '2', 'mandatory' => '1'],
            ['field' => '99', 'mandatory' => ''],   // deleted field
            ['field' => '3', 'mandatory' => ''],    // disabled field
            ['field' => '1', 'mandatory' => '0'],
            ['field' => '4', 'mandatory' => ''],
        ]);
        $this->assertSame(['diet', 'ward_area', 'approved'], array_column($r, 'key'));
        $this->assertSame([true, false, false], array_column($r, 'mandatory'));
    }

    public function testResolveUsesFieldDefaultForEmptyOverride(): void
    {
        $r = (new RegistrationFields(self::ROWS))->resolve([['field' => '1', 'mandatory' => '']]);
        $this->assertTrue($r[0]['mandatory']);
    }

    public function testResolveAcceptsSerializedSelectionAndIgnoresForeignRows(): void
    {
        $f = new RegistrationFields(self::ROWS);
        $this->assertSame(['ward_area'], array_column($f->resolve(serialize([['field' => '1', 'mandatory' => '']])), 'key'));
        // rows written by another extension under the same column name
        $this->assertSame([], $f->resolve([['subscription_fields_field' => 'role', 'subscription_fields_mandatory' => '1']]));
    }

    public function testResolveListsEachFieldOnce(): void
    {
        $r = (new RegistrationFields(self::ROWS))->resolve([['field' => '1', 'mandatory' => ''], ['field' => '1', 'mandatory' => '0']]);
        $this->assertCount(1, $r);
    }

    public function testEmptySelectionGivesNoFields(): void
    {
        $f = new RegistrationFields(self::ROWS);
        $this->assertSame([], $f->resolve(null));
        $this->assertSame([], $f->resolve(''));
        $this->assertSame([], $f->resolve([]));
    }

    public function testFormFieldConfig(): void
    {
        $f = new RegistrationFields(self::ROWS);
        [$diet] = $f->resolve([['field' => '2', 'mandatory' => '1']]);
        $c = $f->formFieldConfig($diet);
        $this->assertSame('select', $c['inputType']);
        $this->assertSame(['None' => 'None', 'Vegan' => 'Vegan'], $c['options']);
        $this->assertTrue($c['eval']['mandatory']);
        $this->assertSame('Diet', $c['label']);
    }

    public function testFormFieldConfigPerType(): void
    {
        $f = new RegistrationFields(self::ROWS);
        $number = $f->formFieldConfig(['key' => 'n', 'label' => 'N', 'type' => 'number', 'options' => [], 'mandatory' => false]);
        $this->assertSame('text', $number['inputType']);
        $this->assertSame('digit', $number['eval']['rgxp']);

        $email = $f->formFieldConfig(['key' => 'e', 'label' => 'E', 'type' => 'email', 'options' => [], 'mandatory' => false]);
        $this->assertSame('email', $email['eval']['rgxp']);
        $this->assertSame(255, $email['eval']['maxlength']);

        $checkbox = $f->formFieldConfig(['key' => 'a', 'label' => 'Approved', 'type' => 'checkbox', 'options' => [], 'mandatory' => true]);
        $this->assertSame('checkbox', $checkbox['inputType']);
        $this->assertSame([1 => 'Approved'], $checkbox['options']);
        $this->assertTrue($checkbox['eval']['mandatory']);
    }

    public function testAllFieldsIncludesDisabledInDefinitionOrder(): void
    {
        $all = (new RegistrationFields(self::ROWS))->allFields();
        $this->assertSame(['ward_area', 'diet', 'old', 'approved'], array_column($all, 'key'));
        $this->assertSame([false, false, false, false], array_column($all, 'mandatory'));
    }

    public function testExportColumnsAndFormatting(): void
    {
        $f = new RegistrationFields(self::ROWS);
        $this->assertSame(['subscription_field_ward_area', 'subscription_field_diet', 'subscription_field_old', 'subscription_field_approved'], array_keys($f->exportColumns()));
        $this->assertSame('Ward / Area', $f->exportColumns()['subscription_field_ward_area']);
        $this->assertSame('registrationField_ward_area', $f->virtualFieldName('ward_area'));
        $approved = $f->resolve([['field' => '4', 'mandatory' => '']])[0];
        $this->assertSame('Yes', $f->formatValue($approved, '1'));
        $this->assertSame('No', $f->formatValue($approved, ''));
        $this->assertSame('No', $f->formatValue($approved, null));
        $this->assertSame('<script>x</script>', $f->formatValue(['type' => 'text'], '<script>x</script>'));
        $this->assertSame('', $f->formatValue(['type' => 'text'], null));
    }

    public function testNoFieldsDefined(): void
    {
        $f = new RegistrationFields([]);
        $this->assertSame([], $f->exportColumns());
        $this->assertSame([], $f->allFields());
        $this->assertSame([], $f->resolve([['field' => '1', 'mandatory' => '']]));
    }
}
