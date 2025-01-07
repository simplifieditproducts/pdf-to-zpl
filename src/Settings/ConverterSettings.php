<?php

namespace Faerber\PdfToZpl\Settings;

/** Settings for the PDF to ZPL conversion */ 
class ConverterSettings {

    /** How the image should be scaled to fit on the label */ 
    public readonly ImageScale $scale; 
    /** Dots Per Inch of the desired Label */ 
    public readonly int $dpi; 

    /** The width in Pixels of your label */
    public readonly int $labelWidth;
    
    /** The height in Pixels of your label */
    public readonly int $labelHeight;
    
    public function __construct(
        ImageScale $scale = ImageScale::Cover,
        int $dpi = 203,
        int $labelWidth = 812,
        int $labelHeight = 1218,
    )
    {
        $this->scale = $scale;
        $this->dpi = $dpi;
        $this->labelWidth = $labelWidth;
        $this->labelHeight = $labelHeight;
    }
}
