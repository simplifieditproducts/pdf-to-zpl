<?php

namespace Faerber\PdfToZpl;

interface ZplConverter {
    /** Read and convert a file into a list of ZPL commands (1 per page) */
    public function convertFromFile(string $filepath): array;
    
    /** Convert a raw blob of binary data into a list of ZPL commands (1 per page) */
    public function convertFromBlob(string $rawData): array;

    /** Get a list of extensions that this converter can convert */
    public function canConvert(): array;
}
