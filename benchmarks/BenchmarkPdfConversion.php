<?php

use Faerber\PdfToZpl\Images\ImageProcessorOption;
use Faerber\PdfToZpl\PdfToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;

/**
 * @Revs(2)
 * @Iterations(2)
 */
class BenchmarkPdfConversion
{
    public static function testFile(string $name): string
    {
        return __DIR__ . "/../test_data/{$name}";
    }

    private function convertFile(string $name, ImageProcessorOption $processorOption)
    {
        $converter = new PdfToZplConverter(
            new ConverterSettings(
                imageProcessorOption: $processorOption,
            )
        );
        $testPath = self::testFile($name);
        $converter->convertFromFile($testPath);
    }

    /**
     * Verify phpbench is working properly
     * @Subject
     */
    public function doAdd()
    {
        $b = 1 + 2;
    }

    /**
     * @Subject
     */
    public function doConvertLabelImagick()
    {
        $this->convertFile("endicia-shipping-label.pdf", ImageProcessorOption::Imagick);
    }

    /**
     * @Subject
     */
    public function doConvertLabelGd()
    {
        $this->convertFile("endicia-shipping-label.pdf", ImageProcessorOption::Gd);
    }

    /**
     * @Subject
     */
    public function doConvertDonkeyImagick()
    {
        $this->convertFile("donkey.pdf", ImageProcessorOption::Imagick);
    }

    /**
     * @Subject
     */
    public function doConvertDonkeyGd()
    {
        $this->convertFile("donkey.pdf", ImageProcessorOption::Gd);
    }

    /**
     * @Subject
     */
    public function doConvertAmericaImagick()
    {
        $this->convertFile("america.pdf", ImageProcessorOption::Imagick);
    }


    /**
     * @Subject
     */
    public function doConvertAmericaGd()
    {
        $this->convertFile("america.pdf", ImageProcessorOption::Gd);
    }
}
