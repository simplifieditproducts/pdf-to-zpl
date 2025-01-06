<?php

namespace PdfToZpl; 

use Exception;
use Illuminate\Support\Collection;

use PdfToZpl\Settings\ConverterSettings;

class PdfToZplConverter {
    const LABEL_WIDTH = 812;
    const LABEL_HEIGHT = 1218;
    const LABEL_DPI = 203;

    public ConverterSettings $settings;

    public function __construct(
        ConverterSettings|null $settings = null,
    )
    {
        $this->settings = $settings ?? new ConverterSettings;
    }

    // Normal sized PDF: A4, Portrait (8.27 × 11.69 inch)
    // Desired sized PDF: prc 32k, Portrait (3.86 × 6.00 inch)

    private function pdfToPrintableZpls(string $pdfData): Collection {
        return $this->pdfToImages($pdfData)
            ->map(ImageToZpl::rawImageToZpl(...));
    }

    /** Add a white background to the label */
    private function background(ImagickStub $img) {
        $background = new ImagickStub();
        $pixel = new ImagickPixelStub('white');
        $background->newImage($img->getImageWidth(), $img->getImageHeight(), $pixel->inner());

        $background->setImageFormat(
            $img->getImageFormat()
        );

        $background->compositeImage($img->inner(), ImagickStub::constant('COMPOSITE_OVER'), 0, 0);

        return $background;
    }

    private function pdfToImages(string $pdfData): Collection {
        $img = new ImagickStub();
        $img->setResolution(self::LABEL_DPI, self::LABEL_DPI);
        $img->readImageBlob($pdfData);

        $pages = $img->getNumberImages();
        $images = collect([]);;
        for ($i = 0; $i < $pages; $i++) {
            $img->setIteratorIndex($i);

            $img->setImageCompressionQuality(100);

            $scale = $this->settings->scale;
            if ($img->getImageWidth() !== self::LABEL_WIDTH && $scale->shouldResize()) {
                $img->scaleImage(self::LABEL_WIDTH, self::LABEL_HEIGHT, bestfit: $scale->isBestFit());
            }

            $img->setImageFormat('png');
            $background = $this->background($img);
            $images->push((string)$background);
        }
        $img->destroy();

        return $images;
    }

    public function convertFromBlob(string $pdfData): array {
        return $this->pdfToPrintableZpls($pdfData)->toArray();
    }

    public function convertFromFile(string $filepath): array {
        $rawData = @file_get_contents($filepath);
        if (! $rawData) {
            throw new Exception("File {$filepath} does not exist!");
        }

        return $this->convertFromBlob($rawData);
    }

    public function test() {

    }
}
