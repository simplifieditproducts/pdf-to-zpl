<?php

namespace Faerber\PdfToZpl\Settings;

use Exception;
use Faerber\PdfToZpl\Images\{ImageProcessorOption, ImageProcessor};
use Symfony\Component\EventDispatcher\DependencyInjection\ExtractingEventDispatcher;

/** Settings for the PDF to ZPL conversion */
class ConverterSettings
{
    public const DEFAULT_LABEL_WIDTH = 812;
    public const DEFAULT_LABEL_HEIGHT = 1218;
    public const DEFAULT_LABEL_DPI = 203;
    
    /** How the image should be scaled to fit on the label */
    public readonly ImageScale $scale;
    /** Dots Per Inch of the desired Label */
    public readonly int $dpi;

    /** The width in Pixels of your label */
    public readonly int $labelWidth;

    /** The height in Pixels of your label */
    public readonly int $labelHeight;

    /** The format to encode the image with */
    public string $imageFormat;

    public ImageProcessor $imageProcessor;

    public function __construct(
        ImageScale $scale = ImageScale::Cover,
        int $dpi = self::DEFAULT_LABEL_DPI,
        int $labelWidth = self::DEFAULT_LABEL_WIDTH,
        int $labelHeight = self::DEFAULT_LABEL_HEIGHT,
        string $imageFormat = "png",
        ImageProcessorOption $imageProcessorOption = ImageProcessorOption::Gd,
    ) {
        $this->scale = $scale;
        $this->dpi = $dpi;
        $this->labelWidth = $labelWidth;
        $this->labelHeight = $labelHeight;
        $this->imageFormat = $imageFormat;
        $this->verifyDependencies($imageProcessorOption);

        $this->imageProcessor = $imageProcessorOption->processor();
    }

    private function verifyDependencies(ImageProcessorOption $option) {
        if (! extension_loaded('gd') && $option === ImageProcessorOption::Gd) {
            throw new Exception("pdf-to-zpl: You must install the GD image library or change imageProcessorOption to ImageProcessOption::Imagick");
        }

        if (! extension_loaded('imagick')) {
            throw new Exception("pdf-to-zpl: You must install the Imagick image library"); 
        }
    }

    public static function default() {
        return new self();
    }
}
