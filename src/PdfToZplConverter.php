<?php

namespace Faerber\PdfToZpl;

use Exception;
use Illuminate\Support\Collection;

use Faerber\PdfToZpl\Settings\ConverterSettings;

/** Converts a PDF file into a list of ZPL commands */
class PdfToZplConverter {
    public ConverterSettings $settings;

    public function __construct(
        ConverterSettings|null $settings = null,
    )
    {
        $this->settings = $settings ?? new ConverterSettings();
    }

    // Normal sized PDF: A4, Portrait (8.27 × 11.69 inch)
    // Desired sized PDF: prc 32k, Portrait (3.86 × 6.00 inch)

    private function pdfToZpls(string $pdfData): Collection {
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

    /**
    * @param string $pdfData Raw PDF data as a string 
    * @return Collection<int, string> A list of raw PNG data as a string 
    */
    private function pdfToImages(string $pdfData): Collection {
        $img = new ImagickStub();
        $dpi = $this->settings->dpi; 
        $img->setResolution($dpi, $dpi);
        $img->readImageBlob($pdfData);

        $pages = $img->getNumberImages();
        $images = collect([]);
        for ($i = 0; $i < $pages; $i++) {
            $img->setIteratorIndex($i);

            $img->setImageCompressionQuality(100);

            $labelWidth = $this->settings->labelWidth;
            $labelHeight = $this->settings->labelHeight;
            $scale = $this->settings->scale;
            if ($img->getImageWidth() !== $labelWidth && $scale->shouldResize()) {
                $img->scaleImage($labelWidth, $labelHeight, bestfit: $scale->isBestFit());
            }

            $img->setImageFormat('png');
            $background = $this->background($img);
            $images->push((string)$background);
        }
        $img->destroy();

        return $images;
    }

    /**
    * Convert raw PDF data into an array of ZPL commands.
    * Each page of the PDF is 1 ZPL command.
    */
    public function convertFromBlob(string $pdfData): array {
        return $this->pdfToZpls($pdfData)->toArray();
    }

    /**
    * Load a PDF file and convert it into an array of ZPL commands.
    * Each page of the PDF is 1 ZPL command.
    */
    public function convertFromFile(string $filepath): array {
        $rawData = @file_get_contents($filepath);
        if (! $rawData) {
            throw new Exception("File {$filepath} does not exist!");
        }

        return $this->convertFromBlob($rawData);
    }
}
