<?php

declare(strict_types=1);

use Faerber\PdfToZpl\PdfToZplConverter;
use PHPUnit\Framework\TestCase;

final class EndiciaTest extends TestCase
{
    private static function testData(string $filename): string
    {
        return __DIR__ . "/../test_data/{$filename}";
    }

    private static function testOutput(string $filename): string
    {
        return __DIR__ . "/../test_output/{$filename}";
    }

    private static function loadExpectedEndiciaOuput(): array
    {
        return array_map(fn($index) => file_get_contents(
            self::testOutput("expected_label_{$index}.zpl")), 
            range(0, 2)
        );
    }

    private static function loadExpectedDonkeyOutput(): array
    {
        return array_map(fn($index) => file_get_contents(
            self::testOutput("expected_donkey_{$index}.zpl")), 
            range(0, 8)
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
            self::loadExpectedEndiciaOuput(),
        );
    }


    public function testCanConvertDonkeyPdf(): void
    {
        $converter = new PdfToZplConverter;
        $pages = $converter->convertFromFile(self::testData("donkey.pdf"));

        // Should have 3 pages
        $this->assertEquals(
            count($pages),
            9
        );

        // Should match the previously generated data
        $this->assertEquals(
            $pages,
            self::loadExpectedDonkeyOutput(),
        );
    }
}
