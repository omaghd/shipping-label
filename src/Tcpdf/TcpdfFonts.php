<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use TCPDF;

final class TcpdfFonts
{
    public const DEFAULT_FONT = 'notosansarabic';

    public static function register(TCPDF $pdf): void
    {
        $fontDirectory = dirname(__DIR__, 2).'/resources/dist/fonts/tcpdf/';

        $pdf->AddFont(self::DEFAULT_FONT, '', $fontDirectory.'notosansarabic.php');
        $pdf->AddFont(self::DEFAULT_FONT, 'B', $fontDirectory.'notosansarabicb.php');
    }
}
