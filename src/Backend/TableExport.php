<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\BackendTemplate;
use Contao\CoreBundle\Exception\ResponseException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

/**
 * One table, sent as Excel, CSV or OpenDocument, or as a print-ready page for PDF (the browser makes the PDF: choose "Save as PDF" when printing)
 */
final class TableExport
{
    public const FORMATS = ['xlsx', 'csv', 'ods', 'pdf'];

    public static function format(mixed $value): string
    {
        return \is_string($value) && \in_array($value, self::FORMATS, true) ? $value : 'csv';
    }

    /**
     * @param list<string>                        $header
     * @param list<list<string|int|float|null>>    $rows
     * @param list<string>                        $meta   Lines under the title of the print page
     */
    public static function send(string $format, string $name, string $title, array $header, array $rows, array $meta = [], bool $landscape = false): never
    {
        $format = self::format($format);

        if ('pdf' === $format) {
            $template = new BackendTemplate('be_events_subscriptions_print');
            $template->title = $title;
            $template->meta = $meta;
            $template->header = $header;
            $template->rows = $rows;
            $template->landscape = $landscape;

            throw new ResponseException(new Response($template->parse()));
        }

        [$body, $type] = match ($format) {
            'xlsx' => [self::spreadsheet(new Xlsx(self::build($title, $header, $rows))), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'ods' => [self::spreadsheet(new Ods(self::build($title, $header, $rows))), 'application/vnd.oasis.opendocument.spreadsheet'],
            default => [self::csv($header, $rows), 'text/csv; charset=utf-8'],
        };

        throw new ResponseException(new Response($body, 200, [
            'Content-Type' => $type,
            'Content-Disposition' => sprintf('attachment; filename="%s.%s"', preg_replace('/[^a-z0-9._-]+/i', '-', $name), $format),
        ]));
    }

    private static function csv(array $header, array $rows): string
    {
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF"); // so Excel reads it as UTF-8

        foreach (array_merge([$header], $rows) as $row) {
            // A text starting with = + - @ would run as a formula when opened in Excel
            fputcsv($out, array_map(static fn ($value) => \is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value, $row), ',', '"', '\\');
        }

        rewind($out);

        return stream_get_contents($out);
    }

    private static function build(string $title, array $header, array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr(trim((string) preg_replace('/[\\\\\/?*\[\]:]+/', ' ', $title)) ?: 'Export', 0, 31));

        foreach (array_merge([$header], $rows) as $r => $row) {
            foreach (array_values($row) as $c => $value) {
                $cell = $sheet->getCell([$c + 1, $r + 1]);

                // Text always stays text, so a title like "=1+1" is never run as a formula
                if (\is_int($value) || \is_float($value)) {
                    $cell->setValue($value);
                } else {
                    $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
                }
            }
        }

        $sheet->getStyle([1, 1, max(1, \count($header)), 1])->getFont()->setBold(true);
        $sheet->freezePane('A2');

        foreach (array_keys($header) as $c) {
            $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    private static function spreadsheet(Xlsx|Ods $writer): string
    {
        $file = tempnam(sys_get_temp_dir(), 'events');

        try {
            $writer->save($file);

            return (string) file_get_contents($file);
        } finally {
            @unlink($file);
        }
    }
}
