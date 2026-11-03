<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests;

use Codefog\EventsSubscriptionsBundle\Exporter;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

/**
 * The booking export (event, calendar, group) lists what guests typed: nothing may run as a formula in Excel.
 */
class ExporterTest extends TestCase
{
    private function export(string $method, array $data): string
    {
        $exporter = (new \ReflectionClass(Exporter::class))->newInstanceWithoutConstructor();
        $file = (new \ReflectionMethod(Exporter::class, $method))->invoke($exporter, $data);
        $path = $file->getPathname();

        return $path;
    }

    public function testCsvDefusesFormulas(): void
    {
        $path = $this->export('exportToCsv', [['Name', 'Places'], ['=HYPERLINK("http://evil.test","x")', 2], ['+1', 1], ['-1', 1], ['@SUM(A1)', 1], ['Anna', 3]]);
        $lines = array_map('str_getcsv', explode("\n", trim(file_get_contents($path))));
        unlink($path);

        foreach ([1, 2, 3, 4] as $i) {
            $this->assertStringStartsWith("'", $lines[$i][0], $lines[$i][0]);
        }

        $this->assertSame('Anna', $lines[5][0]);
        $this->assertSame('3', $lines[5][1]);
    }

    public function testExcelKeepsTextAsText(): void
    {
        $path = $this->export('exportToExcel', [['Name', 'Places'], ['=1+1', 2], ['@x', 1.5]]);
        $sheet = IOFactory::load($path)->getActiveSheet();
        unlink($path);

        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A2')->getDataType());
        $this->assertSame('=1+1', $sheet->getCell('A2')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A3')->getDataType());
        $this->assertSame(DataType::TYPE_NUMERIC, $sheet->getCell('B2')->getDataType());
    }
}
