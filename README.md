# PDF to ZPL ![Packagist](https://img.shields.io/packagist/v/faerber/pdf-to-zpl) 


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
Then make sure to enable them in `php.ini`.


### Imagick Settings
You may need to enable PDF permission in your Imagick settings.

The easiest way to do this is by running the setup shell script: `sudo ./scripts/configure-imagick.sh`

If the script doesn't meet your needs you can perform the change manually.
First edit your Imagick Policy Folder with: `sudo nano "/etc/$(ls /etc/ | grep ImageMagick)/policy.xml"`

Find this line and ensure the rights are set to `read | write`:
```
<policy domain="coder" rights="none" pattern="PDF" />
```
Change to:
```
<policy domain="coder" rights="read | write" pattern="PDF" />
```

I've only tested this library on Linux and Mac so if you get it working on windows feel free to open a PR!

## Unit Testing
Testing is done via PHP Unit. `composer install` and then use `composer test`.


## How does this work?
1. Load the PDF and seperate it into pages
1. Convert each page into a grayscaled bitmap
1. Run line encode the bitmap and marshal it into a ZPL binary representation
1. Wrap the encoded data into a ZPL payload
