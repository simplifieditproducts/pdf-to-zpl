<?php

declare(strict_types=1);

use Faerber\PdfToZpl\ZplConverterFactory;
use PHPUnit\Framework\TestCase;

final class CanUseFactoryTest extends TestCase
{

    public function testCanUseFactoryForImage() {
        $duck = TestUtils::testData("duck.png");
        $converter = ZplConverterFactory::converterFromFile($duck);
        $pages = $converter->convertFromFile($duck);
        $expectedPageCount = 1;

        $this->assertEquals(
            count($pages),
            $expectedPageCount,
        );

        // Should match the previously generated data
        $this->assertEquals(
            $pages,
            TestUtils::loadExpectedPages("expected_duck", count($pages)),
        );
    }


    public function testCanUseFactoryForPdf(): void
    {
        $pdf = TestUtils::testData("endicia-shipping-label.pdf");
        $converter = ZplConverterFactory::converterFromFile($pdf);
        $pages = $converter->convertFromFile($pdf);
        $expectedPageCount = 3;

        // Should have 3 pages
        $this->assertEquals(
            count($pages),
            $expectedPageCount,
        );

        // Should match the previously generated data
        $this->assertEquals(
            $pages,
            TestUtils::loadExpectedPages("expected_label", count($pages)),
        );
    }
}
