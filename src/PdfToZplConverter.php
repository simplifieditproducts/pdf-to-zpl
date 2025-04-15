<?php

namespace Faerber\PdfToZpl;

use Exception;
use Faerber\PdfToZpl\Images\ImagickProcessor;
use Illuminate\Support\Collection;
use Faerber\PdfToZpl\Settings\ConverterSettings;

/** Converts a PDF file into a list of ZPL commands */
class PdfToZplConverter implements ZplConverterService {
    public ConverterSettings $settings;
    private ImageToZplConverter $imageConverter;
    private const IMAGICK_SECURITY_CODE = 499;

    public function __construct(
        ConverterSettings|null $settings = null,
    ) {
        $this->settings = $settings ?? new ConverterSettings();
        $this->imageConverter = new ImageToZplConverter($this->settings);
    }

    // Normal sized PDF: A4, Portrait (8.27 × 11.69 inch)
    // Desired sized PDF: prc 32k, Portrait (3.86 × 6.00 inch)

    private function pdfToZpls(string $pdfData): Collection {
        return $this->pdfToImages($pdfData)
            ->map(fn ($img) => $this->imageConverter->rawImageToZpl($img));
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
        try {
            $img->readImageBlob($pdfData);
            $this->settings->log("Read blob...");
        } catch (Exception $e) {
            /** @disregard intelephense(P1009) */
            if (is_a($e, \ImagickException::class) && $e->getCode() === self::IMAGICK_SECURITY_CODE) {
                throw new Exception("You need to enable PDF reading and writing in your Imagick settings (see docs for more details)", code: 10, previous: $e);
            }
            // No special handling
            throw $e;
        }

        $pages = $img->getNumberImages();
        $this->settings->log("Page count = " . $pages);
        $processor = new ImagickProcessor($img, $this->settings);

        $images = new Collection([]);
        for ($i = 0; $i < $pages; $i++) {
            $this->settings->log("Working on page " . $i);
            $img->setIteratorIndex($i);

            $img->setImageCompressionQuality(100);

            $processor
                ->scaleImage()
                ->rotateImage();

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
        $this->settings->log("File Size for {$filepath} is " . strlen($rawData));

        return $this->convertFromBlob($rawData);
    }

    /** Extensions this converter is able to process */
    public static function canConvert(): array {
        return ["pdf"];
    }
}
