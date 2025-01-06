# PDF to ZPL

Convert a PDF into the ZPL format.

## Gettings Started: 
```
composer require faerber/pdf-to-zpl
```

```php
<?php
use Faerber\PdfToZpl\PdfToZplConverter;

$converter = new PdfToZplConverter();

// Get an array of ZPL commands (1 per page)
$pages = $converter->convertFromFile("myFile.pdf");

foreach ($pages as $index => $page) {
    // Each page is a single ZPL statement
    assert(str_starts_with($page, "^XA^GFA,"));
}
```

## Environment Setup:

Ensure you have Imagick and GD installed using:
```
sudo apt install php8.4-gd

sudo apt install php8.4-imagick
```

### Imagick Settings
You may need to enable PDF permission in your Imagick settings.

First edit your Imagick Policy Folder with: `cd /etc && nano "$(ls | grep ImageMagick)/policy.xml"`

Find this line and ensure the rights are set to `read | write`:
```
<policy domain="coder" rights="none" pattern="PDF" />
```
Change to:
```
<policy domain="coder" rights="read | write" pattern="PDF" />
```


## How does this work?
1. Load the PDF and seperate it into pages
1. Convert each page into a grayscaled bitmap
1. Run line encode the bitmap and marshal it into a ZPL binary representation
1. Wrap the encoded data into a ZPL payload
