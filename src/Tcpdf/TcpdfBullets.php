<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use TCPDF;

final class TcpdfBullets
{
    public function buildLines(
        TCPDF $pdf,
        array $items,
        string $font,
        float $fontSize,
        float $lineHeight,
        string $bullet,
        float $maxWidth,
        int $maxLinesPerItem,
        string $ellipsis,
        bool $boldDefault
    ): array {
        $lines = [];
        foreach ($items as $item) {
            $runs = TcpdfTextRuns::fromMarkdown((string) $item, $font, $fontSize, $boldDefault);
            $bulletWidth = TcpdfTextRuns::measurePlainWidth($pdf, $bullet, $font, '', $fontSize);
            $available = max(0.0, $maxWidth - ($bulletWidth + 1.2));
            $wrapped = TcpdfTextRuns::wrapRunsToLines($pdf, $runs, $available, $maxLinesPerItem, $ellipsis);
            foreach ($wrapped as $index => $lineRuns) {
                $lines[] = new BulletLine($lineRuns, $index === 0 ? $bullet : '');
            }
        }

        return $lines;
    }
}

final readonly class BulletLine
{
    public function __construct(
        public array $runs,
        public string $bullet
    ) {}
}
