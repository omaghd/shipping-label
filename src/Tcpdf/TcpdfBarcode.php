<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use TCPDF;

final class TcpdfBarcode
{
    public function draw(
        TCPDF $pdf,
        string $code,
        string $type,
        float $x,
        float $y,
        float $width,
        float $height,
        HorizontalAlign $horizontalAlign,
        float $targetWidth
    ): void {
        if ($code === '' || $height <= 0.0 || $width <= 0.0) {
            return;
        }

        $drawWidth = $targetWidth > 0.0 ? min($targetWidth, $width) : $width;
        $offsetX = match ($horizontalAlign) {
            HorizontalAlign::End => $x + max(0.0, $width - $drawWidth),
            HorizontalAlign::Center => $x + max(0.0, ($width - $drawWidth) / 2),
            default => $x,
        };

        $style = [
            'position' => 'S',
            'border' => false,
            'padding' => 0,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
            'text' => true,
            'font' => TcpdfFonts::DEFAULT_FONT,
            'fontsize' => 6,
            'stretchtext' => 4,
        ];

        $pdf->write1DBarcode($code, $type, $offsetX, $y, $drawWidth, $height, 0.4, $style, 'N');
    }
}
