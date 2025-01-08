<?php

require __DIR__ . "/../vendor/autoload.php";

use Faerber\PdfToZpl\ImageToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;
use Faerber\PdfToZpl\Settings\ImageScale;

$size = 170;
$converter = new ImageToZplConverter(
    new ConverterSettings(
        labelWidth: $size * 1.5,
        labelHeight: $size * 0.7,
    )
);

$testData = __DIR__ . "/../test_data";
$testOutput = __DIR__ . "/../test_output";

[$zpl] = $converter->convertFromFile($testData . "/duck.png");

file_put_contents($testOutput . "/tiny-duck.zpl.txt", '$tinyDuck = "' . $zpl . '"');
