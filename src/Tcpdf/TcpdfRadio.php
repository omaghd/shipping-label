<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use OmaghD\ShippingLabel\Enums\VerticalAlign;
use TCPDF;

final class TcpdfRadio
{
    public function draw(
        TCPDF $pdf,
        string $label,
        string $selectedLabel,
        string $unselectedLabel,
        bool $selected,
        float $x,
        float $y,
        float $width,
        float $height,
        float $boxSize,
        float $fontSize,
        float $lineHeight,
        bool $rtl,
        HorizontalAlign $horizontalAlign,
        VerticalAlign $verticalAlign
    ): void {
        $fontSize = $fontSize > 0.0 ? $fontSize : 8.0;
        $lineHeight = $lineHeight > 0.0 ? $lineHeight : ($fontSize * 0.3527777778) * 1.2;

        $baselineY = $this->resolveVerticalStart($y, $height, $lineHeight, $verticalAlign);
        $textY = $baselineY + ($lineHeight - ($fontSize * 0.3527777778)) / 2;
        $boxY = $baselineY + ($lineHeight - $boxSize) / 2;

        $pdf->setRTL(false);
        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, 'B', $fontSize);
        $labelWidth = $pdf->GetStringWidth($label);

        $yesLabel = $selectedLabel;
        $noLabel = $unselectedLabel;

        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, $selected ? 'B' : '', $fontSize);
        $yesWidth = $pdf->GetStringWidth($yesLabel);
        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, $selected ? '' : 'B', $fontSize);
        $noWidth = $pdf->GetStringWidth($noLabel);

        $gapBoxText = 1.0;
        $gapOptions = 3.0;

        $groupWidth = $boxSize + $gapBoxText + $yesWidth + $gapOptions + $boxSize + $gapBoxText + $noWidth;
        $totalWidth = $labelWidth + $gapOptions + $groupWidth;

        $startX = match ($horizontalAlign) {
            HorizontalAlign::End => $x + max(0.0, $width - $totalWidth),
            HorizontalAlign::Center => $x + max(0.0, ($width - $totalWidth) / 2),
            default => $x,
        };

        if ($rtl) {
            $labelX = $startX + $groupWidth + $gapOptions;
            $noGroupX = $startX;
            $yesGroupX = $startX + $boxSize + $gapBoxText + $noWidth + $gapOptions;
        } else {
            $labelX = $startX;
            $yesGroupX = $startX + $labelWidth + $gapOptions;
            $noGroupX = $yesGroupX + $boxSize + $gapBoxText + $yesWidth + $gapOptions;
        }

        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, 'B', $fontSize);
        $pdf->Text($labelX, $textY, $label);

        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, $selected ? 'B' : '', $fontSize);
        $this->drawOption($pdf, $yesGroupX, $textY, $boxY, $boxSize, $gapBoxText, $yesLabel, $selected);

        $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, $selected ? '' : 'B', $fontSize);
        $this->drawOption($pdf, $noGroupX, $textY, $boxY, $boxSize, $gapBoxText, $noLabel, ! $selected);
    }

    private function resolveVerticalStart(float $y, float $height, float $lineHeight, VerticalAlign $verticalAlign): float
    {
        if ($lineHeight >= $height) {
            return $y;
        }

        return match ($verticalAlign) {
            VerticalAlign::Bottom => $y + ($height - $lineHeight),
            VerticalAlign::Middle => $y + (($height - $lineHeight) / 2),
            default => $y,
        };
    }

    private function drawOption(
        TCPDF $pdf,
        float $x,
        float $textY,
        float $boxY,
        float $boxSize,
        float $gap,
        string $text,
        bool $checked
    ): void {
        $pdf->SetLineWidth(0.15);
        $pdf->RoundedRect($x, $boxY, $boxSize, $boxSize, 0.5, '1111', 'D');

        if ($checked) {
            $x1 = $x + ($boxSize * 0.20);
            $y1 = $boxY + ($boxSize * 0.55);
            $x2 = $x + ($boxSize * 0.45);
            $y2 = $boxY + ($boxSize * 0.80);
            $x3 = $x + ($boxSize * 0.85);
            $y3 = $boxY + ($boxSize * 0.20);

            $checkStyle = [
                'width' => 0.6,
                'cap' => 'round',
                'join' => 'round',
                'color' => [0, 0, 0],
            ];

            $pdf->Line($x1, $y1, $x2, $y2, $checkStyle);
            $pdf->Line($x2, $y2, $x3, $y3, $checkStyle);
            $pdf->SetLineWidth(0.1);
        }

        $pdf->Text($x + $boxSize + $gap, $textY, $text);
    }
}
