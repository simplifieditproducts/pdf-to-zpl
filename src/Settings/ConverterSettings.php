<?php

namespace PdfToZpl\Settings;

class ConverterSettings {
    public function __construct(
        public ImageScale $scale = ImageScale::Cover,
    )
    {}
}
