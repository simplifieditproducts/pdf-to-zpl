<?php

use Faerber\PdfToZpl\PdfToZplConverter;

/**
 * @Revs(10)
 * @Iterations(1)
 */
class BenchmarkPdfConversion {

    public static function testFile(string $name): string {
        return __DIR__ . "/../test_data/{$name}";
    }

    private function convertFile(string $name) {
        $converter = new PdfToZplConverter(); 
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
    public function doConvertLabel()
    {
        $this->convertFile("endicia-shipping-label.pdf");
    }


    /**
     * @Subject
     */
    public function doConvertDonkey()
    {
        $this->convertFile("donkey.pdf");
    }


    /**
     * @Subject
     */
    public function doConvertAmerica()
    {
        $this->convertFile("america.pdf");
    }
}
