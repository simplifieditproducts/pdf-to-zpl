<?php

namespace Faerber\PdfToZpl\Images;

use Exception;
use Faerber\PdfToZpl\ImagickStub;
use Faerber\PdfToZpl\Settings\ConverterSettings;

enum ImageProcessorOption {
    case Gd;
    case Imagick;

    public function processor(): ImageProcessor {
        return match ($this) {
            self::Imagick => new ImagickProcessor(new ImagickStub()),
            self::Gd => new GdProcessor, 
        };
    }
}
