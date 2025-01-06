<?php

require __DIR__ . "/../vendor/autoload.php";

use Faerber\PdfToZpl\LabelImage;
use Faerber\PdfToZpl\PdfToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;

$testData = __DIR__ . "/../test_data";
$testOutput = __DIR__ . "/../test_output";
$endiciaShippingLabel = $testData . "/endiciaShippingLabel.pdf";

$converter = new PdfToZplConverter();
$pages = $converter->convertFromFile($endiciaShippingLabel);

foreach ($pages as $index => $page) {
    $zplFile = $testOutput . "/page_{$index}.zpl"; 
    $pngFile = $testOutput . "/page_{$index}.png"; 

    $image = new LabelImage(zpl: $page);
    $image->saveAs($pngFile);
}
