<?php

namespace Faerber\PdfToZpl\Settings;

use Faerber\PdfToZpl\Images\{ImageProcessorOption, ImageProcessor};

/** Settings for the PDF to ZPL conversion */
class ConverterSettings
{
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
        int $dpi = 203,
        int $labelWidth = 812,
        int $labelHeight = 1218,
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
