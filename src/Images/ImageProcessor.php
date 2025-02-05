<?php

namespace Faerber\PdfToZpl\Images;

use Faerber\PdfToZpl\Settings\ConverterSettings;

interface ImageProcessor
{
    public function width(): int;
    public function height(): int;
    public function isPixelBlack(int $x, int $y): bool;

    public function readBlob(string $data);
    public function scaleImage(): static;
    public function rotateImage(): static;
    public function processorType(): ImageProcessorOption;
}
