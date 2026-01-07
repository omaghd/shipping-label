<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use TCPDF;

final class TcpdfQrCode
{
    public function draw(
        TCPDF $pdf,
        string $content,
        string $errorCorrection,
        float $x,
        float $y,
        float $width,
        float $height,
        HorizontalAlign $horizontalAlign,
        float $targetSize
    ): void {
        if ($content === '' || $width <= 0.0 || $height <= 0.0) {
            return;
        }

        $size = $targetSize > 0.0 ? min($targetSize, $width, $height) : min($width, $height);
        $offsetY = $y + max(0.0, ($height - $size) / 2);
        $offsetX = match ($horizontalAlign) {
            HorizontalAlign::End => $x + max(0.0, $width - $size),
            HorizontalAlign::Center => $x + max(0.0, ($width - $size) / 2),
            default => $x,
        };

        $style = [
            'border' => false,
            'padding' => 0,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
        ];

        $pdf->write2DBarcode($content, 'QRCODE,'.$errorCorrection, $offsetX, $offsetY, $size, $size, $style, 'N');
    }
}
