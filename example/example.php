<?php

require __DIR__ . "/../vendor/autoload.php";

use Faerber\PdfToZpl\LabelImage;
use Faerber\PdfToZpl\PdfToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;
use Faerber\PdfToZpl\Settings\ImageScale;

$testData = __DIR__ . "/../test_data";
$testOutput = __DIR__ . "/../test_output";

$settings = new ConverterSettings(
    scale: ImageScale::Cover,
);
$converter = new PdfToZplConverter($settings);

function convertEndiciaLabel() {
    global $converter, $testData, $testOutput;
    $endiciaShippingLabel = $testData . "/endiciaShippingLabel.pdf";
    $pages = $converter->convertFromFile($endiciaShippingLabel);

    foreach ($pages as $index => $page) {
        assert(str_starts_with($page, "^XA^GFA,"));

        $pngFile = $testOutput . "/page_{$index}.png"; 

        $image = new LabelImage(zpl: $page);
        $image->saveAs($pngFile);
    }
}

function convertDonkeyPdf() {
    global $converter, $testData, $testOutput;
    $donkeyPdf = $testData . "/donkey.pdf";

    $pages = $converter->convertFromFile($donkeyPdf);

    foreach ($pages as $index => $page) {
        $pngFile = $testOutput . "/donkey_{$index}.png"; 

        echo "Downloading {$pngFile}";
        $image = new LabelImage(zpl: $page);
        $image->saveAs($pngFile);
    }
}

convertDonkeyPdf();
