# PDF to ZPL

Convert a PDF into the ZPL format.

## Getting Started:
```php
<?php
use Faerber\PdfToZpl\PdfToZplConverter;

$converter = new PdfToZplConverter();

// Get an array of ZPL commands (1 per page)
$pages = $converter->convertFromFile("myFile.pdf");

foreach ($pages as $page) {
    // Each page is a single ZPL statement
    assert(str_starts_with($page, "^XA^GFA,"));
}
```


## How does this work?
1. Load the PDF and seperate it into pages
1. Convert each page into a grayscaled bitmap
1. Run line encode the bitmap and marshal it into a ZPL binary representation
1. Wrap the encoded data into a ZPL payload
