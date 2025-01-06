<?php

namespace Faerber\PdfToZpl; 

use App\Helpers\TestFunction;
use App\Http\Controllers\RateShopper\AsyncHttp;
use App\Http\Controllers\RateShopper\Utils;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use resource;
use Exception;
use Faerber\PdfToZpl\Settings\LabelDirection;

/**
 * A binary PNG image of a ZPL label.
 * You can get this from the labelary API.
 */
class LabelImage {
    private GuzzleClient $httpClient;
    public const URL = "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/";
    public string $image;

    public function __construct(
        public string $zpl,
        public LabelDirection $direction = LabelDirection::Up,
    ) {
        $this->httpClient = new GuzzleClient();
        $this->image = $this->download();
    }

    public function download(): string {
        $headers = [
            'Accept' => 'image/png',
            'X-Rotation' => strval($this->direction->toDegree()),
        ];

        $response = $this->httpClient->post(self::URL, [
            'headers' => $headers,
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => $this->zpl,
                ]
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to Download Image!");
        }

        return (string)$response->getBody();
    }

    private function checkImageReady() {
        if (is_null($this->image)) {
            throw new Exception("Image not downloaded yet!");
        }
    } 

    /**
    * For use in HTML image tags. `<img src="{{ $label->asHtmlImage() }}" />`
    */
    public function asHtmlImage(): string {
        $this->checkImageReady();
        return "data:image/png;base64," . base64_encode($this->image);
    }

    /** A raw binary data of the image. Can be saved to disk or uploaded */
    public function asRaw() {
        return $this->image;
    }

    /**
    * Use the binary form of this image in a ZPL statement
    * This bypasses the printer's font encoder allowing any
    * character / font
    */
    public function toZpl(): string {
        return ImageToZpl::rawImageToZpl($this->asRaw());
    }

    public function saveAs(string $filepath) {
        file_put_contents($filepath, $this->asRaw());
    }
}
