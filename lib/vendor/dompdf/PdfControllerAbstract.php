<?php

namespace WHMCS\Module\Addon\LegalEntities\vendor\dompdf;

require_once 'original/lib/html5lib/Parser.php';
require_once 'original/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'original/lib/php-svg-lib/src/autoload.php';
require_once 'original/src/Autoloader.php';

class PdfControllerAbstract
{
    public static function init()
    {
        \Dompdf\Autoloader::register();
    }

    public static function renderFromHtml($html, $orientation = 'portrait')
    {
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $options = $dompdf->getOptions();
        $options->setFontHeightRatio(1);
        $dompdf->setOptions($options);
        $dompdf->render();
        return $dompdf->output();
    }
}