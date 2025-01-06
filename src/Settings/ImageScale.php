<?php

namespace PdfToZpl\Settings;

enum ImageScale {
    case Fill;
    case Cover;
    case None;

    public function shouldResize(): bool {
        return $this === self::Fill || $this === self::Cover;
    }

    public function isBestFit(): bool {
        return $this === self::Cover;
    }
}
