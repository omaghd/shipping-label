<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use OmaghD\ShippingLabel\Config\LabelConfig;
use TCPDF;

final class TcpdfFactory
{
    public function make(LabelConfig $config): TCPDF
    {
        $pdf = new TCPDF($config->getOrientation()->value, $config->getUnit()->value, [$config->getPageWidth(), $config->getPageHeight()], true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setCellPadding(0);
        $pdf->setCellMargins(0, 0, 0, 0);

        TcpdfFonts::register($pdf);
        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, '', $config->getDefaultTextStyle()->getFontSize());

        return $pdf;
    }
}
