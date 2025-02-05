<?php

namespace Faerber\PdfToZpl\Images;

use Exception;
use Faerber\PdfToZpl\ImagickStub;
use Faerber\PdfToZpl\Settings\ConverterSettings;

enum ImageProcessorOption
{
    /**
    * The faster and better processing option, it needs to be installed
    */
    case Gd;

    /**
    * The slower and worse processing option,
    * it is installed by default and is useful in environments where you cannot install extensions
    */
    case Imagick;

    public function processor(ConverterSettings $settings): ImageProcessor
    {
        return match ($this) {
            self::Imagick => new ImagickProcessor(new ImagickStub(), $settings),
            self::Gd => new GdProcessor($settings),
        };
    }
}
