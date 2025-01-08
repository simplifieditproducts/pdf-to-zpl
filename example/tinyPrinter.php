<?php

require __DIR__ . "/../vendor/autoload.php";

use Faerber\PdfToZpl\ImageToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;

$converter = new ImageToZplConverter(
    new ConverterSettings(
        labelWidth: (ConverterSettings::DEFAULT_LABEL_WIDTH / 3) - 50,
        labelHeight: (ConverterSettings::DEFAULT_LABEL_HEIGHT / 6) - 50,
    )
);

$testData = __DIR__ . "/../test_data";
$testOutput = __DIR__ . "/../test_output";

[$zpl] = $converter->convertFromFile($testData . "/duck.png");

file_put_contents($testOutput . "/tiny-duck.zpl.txt", $zpl);
