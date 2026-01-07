<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use OmaghD\ShippingLabel\Enums\ImageFit;
use OmaghD\ShippingLabel\Support\Svg;
use TCPDF;

final class TcpdfImage
{
    public function draw(
        TCPDF $pdf,
        string $path,
        float $x,
        float $y,
        float $width,
        float $height,
        float $targetWidth,
        float $targetHeight,
        ImageFit $fit,
        HorizontalAlign $horizontalAlign
    ): void {
        if ($path === '' || ! is_file($path) || $width <= 0.0 || $height <= 0.0) {
            return;
        }

        $ratio = $this->aspectRatio($path);
        if ($ratio <= 0.0) {
            $ratio = 1.0;
        }

        $boxWidth = $width;
        $boxHeight = $height;
        $maxWidth = $targetWidth > 0.0 ? min($targetWidth, $boxWidth) : $boxWidth;
        $maxHeight = $targetHeight > 0.0 ? min($targetHeight, $boxHeight) : $boxHeight;

        $drawWidth = $maxWidth;
        $drawHeight = $maxHeight;

        if ($fit === ImageFit::Contain) {
            $drawWidth = min($maxWidth, $maxHeight * $ratio);
            $drawHeight = $ratio > 0.0 ? ($drawWidth / $ratio) : $maxHeight;
        }

        if ($fit === ImageFit::Cover) {
            $drawWidth = max($maxWidth, $maxHeight * $ratio);
            $drawHeight = $ratio > 0.0 ? ($drawWidth / $ratio) : $maxHeight;
        }

        $drawWidth = min($drawWidth, $boxWidth);
        $drawHeight = min($drawHeight, $boxHeight);

        $offsetY = $y + max(0.0, ($boxHeight - $drawHeight) / 2);
        $offsetX = match ($horizontalAlign) {
            HorizontalAlign::End => $x + max(0.0, $boxWidth - $drawWidth),
            HorizontalAlign::Center => $x + max(0.0, ($boxWidth - $drawWidth) / 2),
            default => $x,
        };

        $pdf->Image(
            $path,
            $offsetX,
            $offsetY,
            $drawWidth,
            $drawHeight,
            '',
            '',
            '',
            false,
            300,
            '',
            false,
            false,
            0,
            false,
            false,
            false
        );
    }

    private function aspectRatio(string $path): float
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'svg') {
            return Svg::aspectRatio($path);
        }

        $info = @getimagesize($path);
        if (! is_array($info) || empty($info[0]) || empty($info[1])) {
            return 1.0;
        }

        return ((float) $info[0]) / ((float) $info[1]);
    }
}
