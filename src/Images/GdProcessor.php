<?php

namespace Faerber\PdfToZpl\Images;

use Exception;
use Faerber\PdfToZpl\ImagickStub;
use Faerber\PdfToZpl\Settings\ConverterSettings;
use GdImage;

class GdProcessor implements ImageProcessor
{
    private GdImage $img;

    public function __construct() {}

    public function width(): int
    {
        return imagesx($this->img);
    }

    public function height(): int
    {
        return imagesy($this->img);
    }

    public function isPixelBlack(int $x, int $y): bool
    {
        return (imagecolorat($this->img, $x, $y) & 0xFF) < 127;
    }

    public function readBlob(string $data): static
    {
        $this->img = imagecreatefromstring($data);
        if (! $this->img) {
            throw new Exception("Failure!");
        }

        imagepalettetotruecolor($this->img);
        return $this;
    }

    public function scaleImage(ConverterSettings $settings): static
    {
        if (!$settings->scale->shouldResize() || $this->width() === $settings->labelWidth) {
            return $this;
        }

        $srcWidth = imagesx($this->img);
        $srcHeight = imagesy($this->img);

        $dstWidth = $settings->labelWidth;
        $dstHeight = $settings->labelHeight;

        if ($settings->scale->isBestFit()) {
            $aspectRatio = $srcWidth / $srcHeight;
            if ($srcWidth > $srcHeight) {
                $dstHeight = (int) ($dstWidth / $aspectRatio);
            } else {
                $dstWidth = (int) ($dstHeight * $aspectRatio);
            }
        }

        $scaledImg = imagecreatetruecolor($dstWidth, $dstHeight);

        imagecopyresampled(
            $scaledImg,
            $this->img,
            0,
            0,
            0,
            0,
            $dstWidth,
            $dstHeight,
            $srcWidth,
            $srcHeight
        );

        $this->img = $scaledImg;

        return $this;
    }

    public function processorType(): ImageProcessorOption
    {
        return ImageProcessorOption::Gd;
    }
}
