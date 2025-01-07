<?php

require __DIR__ . "/../vendor/autoload.php";

use Faerber\PdfToZpl\LabelImage;
use Faerber\PdfToZpl\PdfToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;
use Faerber\PdfToZpl\Settings\ImageScale;
use Faerber\PdfToZpl\ImageToZpl;
use Faerber\PdfToZpl\ImageToZplConverter;
use Faerber\PdfToZpl\ImageToZplImagickConverter;

$testData = __DIR__ . "/../test_data";
$testOutput = __DIR__ . "/../test_output";

$settings = new ConverterSettings(
    scale: ImageScale::Cover,
);
$converter = new PdfToZplConverter($settings);
$imageConverter = new ImageToZplImagickConverter($settings);

function downloadPages(array $pages, string $name) {
    global $testOutput; 
    foreach ($pages as $index => $page) {
        assert(str_starts_with($page, "^XA^GFA,"));

        $basePath = $testOutput . "/{$name}_{$index}";
        file_put_contents($basePath . ".zpl.txt", $page);

        echo "Downloading {$name} {$index}\n";

        $image = new LabelImage(zpl: $page);
        $image->saveAs($basePath . ".png");

        // So we don't get rate limited
        sleep(5);
    }
}


function convertPdfToPages(string $pdf, string $name)
{
    global $converter, $testData, $testOutput;
    $pdfFile = $testData . "/" . $pdf;
    $pages = $converter->convertFromFile($pdfFile);
    downloadPages($pages, $name);
}

function convertImageToPages(string $image, string $name) {
    global $imageConverter, $testData, $testOutput;
    $imageFile = $testData . "/" . $image;
    $pages = $imageConverter->convertFromFile($imageFile);
    downloadPages($pages, $name); 
}


function convertEndiciaLabel()
{
    convertPdfToPages("endicia-shipping-label.pdf", "expected_label");
}

function convertDonkeyPdf()
{
    convertPdfToPages("donkey.pdf", "expected_donkey");
}

function convertDuckImage()
{
    convertImageToPages("duck.png", "expected_duck");
}

function purgeOld()
{
    global $testOutput;
    foreach (scandir($testOutput) as $file) {
        if (str_starts_with($file, ".")) {
            continue;
        }

        unlink($testOutput . "/" . $file);
    }
}

purgeOld();
convertEndiciaLabel();
// convertDonkeyPdf();
// convertDuckImage();
