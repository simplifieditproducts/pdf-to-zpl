<?php

namespace Faerber\PdfToZpl;

use Faerber\PdfToZpl\ImageToZplConverter;
use Faerber\PdfToZpl\PdfToZplConverter;
use Faerber\PdfToZpl\Settings\ConverterSettings;
use Faerber\PdfToZpl\ZplConverterService;

class ZplConverterFactory
{
    public static function converterFromFile(string $filepath, ConverterSettings|null $settings = null): ZplConverterService
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        $settings ??= new ConverterSettings();
        $services = [PdfToZplConverter::class, ImageToZplConverter::class];
        foreach ($services as $service) {
            if (in_array($ext, $service::canConvert())) {
                return new $service($settings);
            }
        }
        throw new Exception("No converter for {$ext} files!");
    }
}
