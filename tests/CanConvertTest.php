<?php

declare(strict_types=1);

use Faerber\PdfToZpl\PdfToZplConverter;
use PHPUnit\Framework\TestCase;

final class CanConvertTest extends TestCase
{
    private static function testData(string $filename): string
    {
        return __DIR__ . "/../test_data/{$filename}";
    }

    private static function testOutput(string $filename): string
    {
        return __DIR__ . "/../test_output/{$filename}";
    }

    private static function loadExpectedPages(string $name, int $pageCount): array
    {
        return array_map(
            fn($index) => file_get_contents(self::testOutput("{$name}_{$index}.zpl")), 
            range(0, $pageCount - 1)
        );
    }

    public function testCanConvertEndiciaPdf(): void
    {
        $converter = new PdfToZplConverter;
        $pages = $converter->convertFromFile(self::testData("endicia-shipping-label.pdf"));

        // Should have 3 pages
        $this->assertEquals(
            count($pages),
            3
        );

        // Should match the previously generated data
        $this->assertEquals(
            $pages,
            self::loadExpectedPages("expected_label", count($pages)),
        );
    }


    public function testCanConvertDonkeyPdf(): void
    {
        $converter = new PdfToZplConverter;
        $pages = $converter->convertFromFile(self::testData("donkey.pdf"));

        // Should have 9 pages
        $this->assertEquals(
            count($pages),
            9
        );

        // Should match the previously generated data
        $this->assertEquals(
            $pages,
            self::loadExpectedPages("expected_donkey", count($pages)),
        );
    }
}
