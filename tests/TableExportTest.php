<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\Backend\TableExport;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PHPUnit\Framework\TestCase;

/**
 * Names and answers typed by guests end up in downloads that people open in Excel: nothing may run as a formula.
 */
class TableExportTest extends TestCase
{
    private function call(string $method, mixed ...$args): mixed
    {
        return (new \ReflectionMethod(TableExport::class, $method))->invoke(null, ...$args);
    }

    public function testCsvNeutralisesFormulas(): void
    {
        $csv = $this->call('csv', ['Name'], [['=HYPERLINK("http://evil.test","x")'], ['+1+1'], ['-2+3'], ['@SUM(A1)'], ["\t=1"], ['Anna'], [12]]);
        $lines = array_map('str_getcsv', array_filter(explode("\n", ltrim($csv, "\xEF\xBB\xBF"))));

        foreach (array_slice($lines, 1, 5) as $line) {
            $this->assertStringStartsWith("'", $line[0], 'formula starter is defused: '.$line[0]);
        }

        $this->assertSame('Anna', $lines[6][0]);
        $this->assertSame('12', $lines[7][0]);
    }

    public function testSpreadsheetKeepsTextAsText(): void
    {
        $sheet = $this->call('build', 'Report', ['Name', 'Count'], [['=1+1', 3], ['@x', 4.5]])->getActiveSheet();

        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A2')->getDataType());
        $this->assertSame('=1+1', $sheet->getCell('A2')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A3')->getDataType());
        $this->assertSame(DataType::TYPE_NUMERIC, $sheet->getCell('B2')->getDataType());
    }

    public function testSheetTitleCannotBreakTheFile(): void
    {
        $title = $this->call('build', 'a/b\\c?d*e[f]g:'.str_repeat('x', 50), ['H'], [])->getActiveSheet()->getTitle();

        $this->assertLessThanOrEqual(31, mb_strlen($title));
        $this->assertDoesNotMatchRegularExpression('/[\\\\\/?*\[\]:]/', $title);
    }
}
