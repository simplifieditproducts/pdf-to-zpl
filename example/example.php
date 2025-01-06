<?php

require __DIR__ . "/../vendor/autoload.php";

use PdfToZpl\PdfToZplConverter;
use PdfToZpl\Settings\ConverterSettings;

$testData = __DIR__ . "/../test_data";
$testOutput = __DIR__ . "/../test_output";
$endiciaShippingLabel = $testData . "/endiciaShippingLabel.pdf";

$converter = new PdfToZplConverter();
$pages = $converter->convertFromFile($endiciaShippingLabel);

foreach ($pages as $index => $page) {
    $filename = $testOutput . "/page_{$index}.zpl"; 
    file_put_contents($filename, $page); 
}
