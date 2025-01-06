<?php

namespace PdfToZpl; 

use App\Helpers\TestFunction;
use App\Http\Controllers\RateShopper\Utils;
use Exception;
use Illuminate\Support\Collection;

class PdfToZplConverter {
    const LABEL_WIDTH = 812;
    const LABEL_HEIGHT = 1218;
    const LABEL_DPI = 203;

    // Normal sized PDF: A4, Portrait (8.27 × 11.69 inch)
    // Desired sized PDF: prc 32k, Portrait (3.86 × 6.00 inch)

    private static function pdfToPrintableZpls(string $pdfData, string $scaleImage): Collection {
        return self::pdfToImages($pdfData, $scaleImage)
            ->map(ImageToZpl::rawImageToZpl(...));
    }

    /** Add a white background to the label */
    private static function background(ImagickStub $img) {
        $background = new ImagickStub();
        $pixel = new ImagickPixelStub('white');
        $background->newImage($img->getImageWidth(), $img->getImageHeight(), $pixel->inner());

        $background->setImageFormat(
            $img->getImageFormat()
        );

        $background->compositeImage($img->inner(), ImagickStub::constant('COMPOSITE_OVER'), 0, 0);

        return $background;
    }

    private static function pdfToImages(string $pdfData, string $scaleImage): Collection {
        $img = new ImagickStub();
        $img->setResolution(self::LABEL_DPI, self::LABEL_DPI);
        $img->readImageBlob($pdfData);

        $pages = $img->getNumberImages();
        $images = collect([]);;
        for ($i = 0; $i < $pages; $i++) {
            $img->setIteratorIndex($i);

            $img->setImageCompressionQuality(100);

            $shouldScale = $scaleImage === 'fill' || $scaleImage === 'cover';
            if ($img->getImageWidth() !== self::LABEL_WIDTH && $shouldScale) {
                $bestFit = $scaleImage === 'cover';
                $img->scaleImage(self::LABEL_WIDTH, self::LABEL_HEIGHT, bestfit: $bestFit);
            }

            $img->setImageFormat('png');
            $background = self::background($img);
            $images->push((string)$background);
        }
        $img->destroy();

        return $images;
    }

    public function convert(string $pdfData, array $settings = []): Collection {
        // fill or bestfit
        $scaleImage = array_key_exists('scale', $settings)
            ? $settings['scale']
            : 'none';

        return self::pdfToPrintableZpls($pdfData, $scaleImage);
    }

    public static function test() {

    }
}
