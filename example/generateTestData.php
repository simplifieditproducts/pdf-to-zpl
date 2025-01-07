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

function convertPdfToPages(string $pdf, string $name)
{
    global $converter, $testData, $testOutput;
    $pdfFile = $testData . "/" . $pdf;
    $pages = $converter->convertFromFile($pdfFile);

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


function convertEndiciaLabel()
{
    convertPdfToPages("endicia-shipping-label.pdf", "expected_label");
}

function convertDonkeyPdf()
{
    convertPdfToPages("donkey.pdf", "expected_donkey");
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
convertDonkeyPdf();
