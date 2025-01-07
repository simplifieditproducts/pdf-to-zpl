<?php

namespace Faerber\PdfToZpl;

use Exception;
use GdImage;
use imagecreatefromstring;
use imagepalettetotruecolor;
use Illuminate\Support\Collection;


/**
 * Convert an Image to Zpl
 *
 * @see https://github.com/himansuahm/php-zpl-converter
 */
class ImageToZpl
{
    public function __construct() {}

    const START_CMD = "^XA";
    const END_CMD = "^XZ";

    public static function convertGdImageToZpl(GdImage $gdImage): string
    {
        // Width in bytes
        $width = (int) ceil(imagesx($gdImage) / 8);
        $height = imagesy($gdImage);
        $bitmap = '';
        $lastRow = null;

        for ($y = 0; $y < $height; $y++) {
            $bits = '';

            // Create a binary string for the row
            for ($x = 0; $x < imagesx($gdImage); $x++) {
                // 1 for black, 0 for white
                $bits .= (imagecolorat($gdImage, $x, $y) & 0xFF) < 127 ? '1' : '0';
            }

            // Convert bits to bytes
            $bytes = str_split($bits, 8);
            $bytes[] = str_pad(array_pop($bytes), 8, '0');

            // Convert bytes to hex and compress
            $row = (new Collection($bytes))
                ->map(fn($byte) => sprintf('%02X', bindec($byte)))
                ->implode('');

            $bitmap .= ($row === $lastRow) ? ':' : self::compressRow(preg_replace(['/0+$/', '/F+$/'], [',', '!'], $row));
            $lastRow = $row;
        }

        // Prepare ZPL command parameters
        $byteCount = $width * $height;
        $parameters = collect(['GF', 'A', $byteCount, $byteCount, $width, $bitmap]);
        $command = strtoupper($parameters->shift());
        $parameters = $parameters->map(fn($p) => (string)$p);

        $dataCommand = "^" . $command . $parameters->implode(",");
        return self::START_CMD . $dataCommand . self::END_CMD;
    }

    /**
     * @param string $rawImage The binary data of an image saved as a string (can be GIF, PNG or JPEG)
     */
    private static function rawImageToGdImage(string $rawImage): GdImage
    {
        $gdImg = imagecreatefromstring($rawImage);
        if (! $gdImg) {
            throw new Exception("Failure!");
        }
        imagepalettetotruecolor($gdImg);
        return $gdImg;
    }

    /** This can just be a string (the first few bytes say if its a GIF or PNG or whatever) */
    public static function rawImageToZpl(string $rawImage): string
    {
        $gdImage = self::rawImageToGdImage($rawImage);
        return self::convertGdImageToZpl($gdImage);
    }

    /** Run Line Encoder (replace repeating characters) */
    private static function compressRow(string $row): string
    {
        return preg_replace_callback('/(.)(\1{2,})/', fn($matches) => self::compressSequence($matches[0]), $row);
    }

    private static function compressSequence(string $sequence): string
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
