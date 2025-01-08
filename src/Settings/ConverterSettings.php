<?php

namespace Faerber\PdfToZpl\Settings;

use Faerber\PdfToZpl\Images\{ImageProcessorOption, ImageProcessor};

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

    /** Which image library to use (Imagick or GD) */
    public ImageProcessorOption $imageProcessorOption;
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
        $this->imageProcessorOption = $imageProcessorOption;
        $this->imageProcessor = $this->imageProcessorOption->processor();
    }

    public static function default() {
        return new self();
    }
}
