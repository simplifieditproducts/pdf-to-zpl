<?php

namespace Faerber\PdfToZpl\Images;

use Exception;
use Faerber\PdfToZpl\ImagickStub;
use Faerber\PdfToZpl\Settings\ConverterSettings;

class ImagickProcessor implements ImageProcessor {
    private ImagickStub $img;

    public function __construct(ImagickStub $img) {
        $this->img = $img;

    } 

    public function width(): int {
        return $this->img->getImageWidth();
    }

    public function height(): int {
        return $this->img->getImageHeight();
    }

    public function isPixelBlack(int $x, int $y): bool
    {
        $pixel = $this->img->getImagePixelColor($x, $y);
        $color = $pixel->getColor();
        $avgColor = ($color['r'] + $color['g'] + $color['b']) / 3;

        return $avgColor < 0.5; 
    }

    public function readBlob(string $data): static {
        $this->img->readImageBlob($data);
        if (! $this->img->readImageBlob($data)) {
            throw new Exception("Cannot load!");
        }

        $this->img->setImageColorspace(ImagickStub::constant("COLORSPACE_RGB"));
        $this->img->setImageFormat('png');
        $this->img->thresholdImage(0.5 * ImagickStub::getQuantum());
        return $this;
    }

    public function scaleImage(ConverterSettings $settings): static 
    {
        if ($this->width() === $settings->labelWidth) {
            return $this;
        } 
        
        if ($settings->scale->shouldResize()) {
            $this->img->scaleImage(
                $settings->labelWidth, 
                $settings->labelHeight, 
                bestfit: $settings->scale->isBestFit()
            );
        }
        return $this;
    }
}
