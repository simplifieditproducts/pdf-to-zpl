<?php

namespace Faerber\PdfToZpl;

use Exception;
use Faerber\PdfToZpl\Settings\ConverterSettings;
use Illuminate\Support\Collection;

/**
 * Convert an Image to Zpl
 *
 * @see https://github.com/himansuahm/php-zpl-converter
 */
class ImageToZplConverter implements ZplConverter
{
    public function __construct(
        public ConverterSettings $settings, 
    )
    {
    }

    public const START_CMD = "^XA";
    public const END_CMD = "^XZ";
    private const ENCODE_CMD = "^GFA";

    public function convertImagickToZpl(ImagickStub $image): string
    {
        // Width in bytes
        $width = (int) ceil($image->getImageWidth() / 8);
        $height = $image->getImageHeight(); 
        $bitmap = '';
        $lastRow = null;

        for ($y = 0; $y < $height; $y++) {
            $bits = '';

            // Create a binary string for the row
            for ($x = 0; $x < $image->getImageWidth(); $x++) {
                $pixel = $image->getImagePixelColor($x, $y);
                $color = $pixel->getColor();
                $avgColor = ($color['r'] + $color['g'] + $color['b']) / 3;

                $bits .= $avgColor < 0.5 ? '1' : '0';
            }

            // Convert bits to bytes
            $bytes = str_split($bits, 8);
            $bytes[] = str_pad(array_pop($bytes), 8, '0');

            // Convert bytes to hex and compress
            $row = (new Collection($bytes))
                ->map(fn ($byte) => sprintf('%02X', bindec($byte)))
                ->implode('');

            $bitmap .= ($row === $lastRow) ? ':' : $this->compressRow(preg_replace(['/0+$/', '/F+$/'], [',', '!'], $row));
            $lastRow = $row;
        }

        // Prepare ZPL command parameters
        $byteCount = $width * $height;
        $parameters = collect([$byteCount, $byteCount, $width, $bitmap]);

        return collect([
            self::START_CMD,
            self::ENCODE_CMD . ",",
            $parameters->implode(","),
           self::END_CMD, 
        ])->implode(''); 
    }

    public function scaleImage(ImagickStub $img): ImagickStub {
        $scale = $this->settings->scale;
        $labelWidth = $this->settings->labelWidth;
        $labelHeight = $this->settings->labelHeight;
        
        if ($img->getImageWidth() === $labelWidth) {
            return $img;
        } 
        
        if ($scale->shouldResize()) {
            $img->scaleImage($labelWidth, $labelHeight, bestfit: $scale->isBestFit());
        }
        return $img;
    }

    /**
     * @param string $rawImage The binary data of an image saved as a string (can be GIF, PNG or JPEG)
     */
    private function rawImageToImagick(string $rawImage): ImagickStub 
    {
        $img = new ImagickStub();
        if (! $img->readImageBlob($rawImage)) {
            throw new Exception("Cannot load!");
        }

        $img->setImageColorspace(ImagickStub::constant("COLORSPACE_RGB"));
        $img->setImageFormat("png");
        $img->thresholdImage(0.5 * ImagickStub::getQuantum());
        return $img;
    }

    /** This can just be a string (the first few bytes say if its a GIF or PNG or whatever) */
    public function rawImageToZpl(string $rawImage): string
    {
        $img = $this->rawImageToImagick($rawImage);
        $img = $this->scaleImage($img);
        return $this->convertImagickToZpl($img);
    }
    
    public function convertFromBlob(string $rawData): array {
        return [$this->rawImageToZpl($rawData)];
    }

    public function convertFromFile(string $filepath): array {
        $rawData = @file_get_contents($filepath);
        if (! $rawData) {
            throw new Exception("Invalid file {$filepath}");
        }
        return $this->convertFromBlob($rawData); 
    }


    /** Run Line Encoder (replace repeating characters) */
    private function compressRow(string $row): string
    {
        return preg_replace_callback('/(.)(\1{2,})/', fn ($matches) => $this->compressSequence($matches[0]), $row);
    }

    private function compressSequence(string $sequence): string
    {
        $repeat = strlen($sequence);
        $count = '';

        if ($repeat > 400) {
            $count .= str_repeat('z', floor($repeat / 400));
            $repeat %= 400;
        }

        if ($repeat > 19) {
            $count .= chr(ord('f') + floor($repeat / 20));
            $repeat %= 20;
        }

        if ($repeat > 0) {
            $count .= chr(ord('F') + $repeat);
        }

        return $count . substr($sequence, 1, 1);
    }
}
