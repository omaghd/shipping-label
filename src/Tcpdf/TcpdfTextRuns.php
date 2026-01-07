<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use TCPDF;

final class TcpdfTextRuns
{
    public static function fromMarkdown(string $text, string $font, float $fontSize, bool $boldDefault): array
    {
        $content = strip_tags($text);
        if ($content === '') {
            return [];
        }

        $parts = preg_split('/(\*\*.*?\*\*)/u', $content, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $runs = [];

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $bold = false;
            if (preg_match('/^\*\*(.*)\*\*$/us', $part, $matches)) {
                $part = $matches[1];
                $bold = true;
            }

            if ($part === '') {
                continue;
            }

            $runs[] = new TextRun(
                $part,
                $font,
                ($boldDefault || $bold) ? 'B' : '',
                $fontSize
            );
        }

        return self::normalize($runs);
    }

    public static function normalize(array $runs): array
    {
        $out = [];
        foreach ($runs as $run) {
            if (! $run instanceof TextRun) {
                continue;
            }
            if ($run->text === '') {
                continue;
            }
            $out[] = $run;
        }

        $merged = [];
        foreach ($out as $run) {
            $last = $merged === [] ? null : $merged[array_key_last($merged)];
            if ($last instanceof TextRun && $last->font === $run->font && $last->style === $run->style && abs($last->fontSize - $run->fontSize) < 0.0001) {
                $merged[array_key_last($merged)] = new TextRun(
                    $last->text.$run->text,
                    $last->font,
                    $last->style,
                    $last->fontSize
                );

                continue;
            }
            $merged[] = $run;
        }

        return $merged;
    }

    public static function measureRunsWidth(TCPDF $pdf, array $runs): float
    {
        $width = 0.0;
        foreach (self::normalize($runs) as $run) {
            $pdf->SetFont($run->font, $run->style, $run->fontSize);
            $width += (float) $pdf->GetStringWidth($run->text);
        }

        return $width;
    }

    public static function measurePlainWidth(TCPDF $pdf, string $text, string $font, string $style, float $fontSize): float
    {
        $pdf->SetFont($font, $style, $fontSize);

        return (float) $pdf->GetStringWidth($text);
    }

    public static function wrapRunsToLines(TCPDF $pdf, array $runs, float $maxWidth, int $maxLines, string $ellipsis): array
    {
        $runs = self::normalize($runs);
        if ($runs === [] || $maxWidth <= 0.0) {
            return [];
        }

        $maxLines = max(1, $maxLines);
        $lines = [];
        $current = [];

        $pushLine = static function () use (&$lines, &$current): void {
            $lines[] = TcpdfTextRuns::normalize($current);
            $current = [];
        };

        foreach ($runs as $run) {
            $tokens = self::tokenizeKeepingSpaces($run->text);
            foreach ($tokens as $token) {
                $candidate = array_merge($current, [new TextRun($token, $run->font, $run->style, $run->fontSize)]);
                if (self::measureRunsWidth($pdf, $candidate) <= $maxWidth) {
                    $current = $candidate;

                    continue;
                }

                if ($current !== []) {
                    $pushLine();

                    if (count($lines) >= $maxLines) {
                        $lines[$maxLines - 1] = self::truncateRunsToWidth($pdf, $lines[$maxLines - 1], $maxWidth, $ellipsis, true);

                        return array_slice($lines, 0, $maxLines);
                    }
                }

                $tokenRun = new TextRun($token, $run->font, $run->style, $run->fontSize);
                if (self::measureRunsWidth($pdf, [$tokenRun]) <= $maxWidth) {
                    $current = [$tokenRun];

                    continue;
                }

                $pieces = self::splitRunToFit($pdf, $tokenRun, $maxWidth);
                if ($pieces === []) {
                    continue;
                }
                $current = [$pieces[0]];
                $counter = count($pieces);
                for ($index = 1; $index < $counter; $index++) {
                    $pushLine();
                    if (count($lines) >= $maxLines) {
                        $lines[$maxLines - 1] = self::truncateRunsToWidth($pdf, $lines[$maxLines - 1], $maxWidth, $ellipsis, true);

                        return array_slice($lines, 0, $maxLines);
                    }
                    $current = [$pieces[$index]];
                }
            }
        }

        if ($current !== []) {
            $pushLine();
        }

        $sliced = false;
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $sliced = true;
        }

        $plain = self::runsToPlainText($runs);
        $needsMore = $sliced || (int) ceil($pdf->getNumLines($plain, $maxWidth)) > $maxLines;

        if ($needsMore && $lines !== []) {
            $lastIndex = array_key_last($lines);
            $lines[$lastIndex] = self::truncateRunsToWidth($pdf, $lines[$lastIndex], $maxWidth, $ellipsis, true);
        }

        return $lines;
    }

    public static function truncateRunsToWidth(TCPDF $pdf, array $runs, float $maxWidth, string $ellipsis, bool $force = false): array
    {
        $runs = self::normalize($runs);
        if ($runs === []) {
            return [];
        }

        if (! $force && self::measureRunsWidth($pdf, $runs) <= $maxWidth) {
            return $runs;
        }

        $first = $runs[0];
        $ellipsisWidth = self::measurePlainWidth($pdf, $ellipsis, $first->font, $first->style, $first->fontSize);
        $target = max(0.0, $maxWidth - $ellipsisWidth);

        $kept = [];
        $used = 0.0;
        foreach ($runs as $run) {
            $runWidth = self::measurePlainWidth($pdf, $run->text, $run->font, $run->style, $run->fontSize);
            if (($used + $runWidth) <= $target) {
                $kept[] = $run;
                $used += $runWidth;

                continue;
            }
            $remaining = max(0.0, $target - $used);
            $cut = self::truncateTextToWidth($pdf, $run, $remaining);
            if ($cut !== '') {
                $kept[] = new TextRun($cut, $run->font, $run->style, $run->fontSize);
            }
            break;
        }

        $kept = self::normalize($kept);
        if ($kept === []) {
            return [new TextRun($ellipsis, $first->font, $first->style, $first->fontSize)];
        }
        $last = $kept[array_key_last($kept)];
        $kept[] = new TextRun($ellipsis, $last->font, $last->style, $last->fontSize);

        return $kept;
    }

    private static function splitRunToFit(TCPDF $pdf, TextRun $run, float $maxWidth): array
    {
        if ($run->text === '') {
            return [];
        }

        $pdf->SetFont($run->font, $run->style, $run->fontSize);
        if ((float) $pdf->GetStringWidth($run->text) <= $maxWidth) {
            return [$run];
        }

        $pieces = [];
        $remaining = $run->text;
        while ($remaining !== '') {
            $cut = self::maxPrefixLenThatFits($pdf, $remaining, $maxWidth);
            if ($cut <= 0) {
                break;
            }
            $part = mb_substr($remaining, 0, $cut, 'UTF-8');
            $pieces[] = new TextRun($part, $run->font, $run->style, $run->fontSize);
            $remaining = mb_substr($remaining, $cut, null, 'UTF-8');
        }

        return $pieces;
    }

    private static function maxPrefixLenThatFits(TCPDF $pdf, string $text, float $maxWidth): int
    {
        $length = mb_strlen($text, 'UTF-8');
        $low = 0;
        $high = $length;
        while ($low < $high) {
            $mid = intdiv($low + $high + 1, 2);
            $candidate = mb_substr($text, 0, $mid, 'UTF-8');
            $width = (float) $pdf->GetStringWidth($candidate);
            if ($width <= $maxWidth) {
                $low = $mid;

                continue;
            }
            $high = $mid - 1;
        }

        return $low;
    }

    private static function tokenizeKeepingSpaces(string $text): array
    {
        $matches = [];
        preg_match_all('/\S+\s*|\s+/u', $text, $matches);

        return $matches[0] ?? [$text];
    }

    private static function truncateTextToWidth(TCPDF $pdf, TextRun $run, float $maxWidth): string
    {
        if ($run->text === '' || $maxWidth <= 0.0) {
            return '';
        }

        $pdf->SetFont($run->font, $run->style, $run->fontSize);
        $length = mb_strlen($run->text, 'UTF-8');
        $low = 0;
        $high = $length;
        while ($low < $high) {
            $mid = intdiv($low + $high + 1, 2);
            $candidate = mb_substr($run->text, 0, $mid, 'UTF-8');
            $width = (float) $pdf->GetStringWidth($candidate);
            if ($width <= $maxWidth) {
                $low = $mid;

                continue;
            }
            $high = $mid - 1;
        }

        return mb_substr($run->text, 0, $low, 'UTF-8');
    }

    private static function runsToPlainText(array $runs): string
    {
        $text = '';
        foreach (self::normalize($runs) as $run) {
            $text .= $run->text;
        }

        return $text;
    }
}

final readonly class TextRun
{
    public function __construct(
        public string $text,
        public string $font,
        public string $style,
        public float $fontSize
    ) {}
}
