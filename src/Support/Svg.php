<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Support;

final class Svg
{
    public static function aspectRatio(string $path): float
    {
        $contents = @file_get_contents($path);
        if (! is_string($contents) || $contents === '') {
            return 1.0;
        }

        if (preg_match('/viewBox\s*=\s*"\s*[-\d\.]+\s+[-\d\.]+\s+([\d\.]+)\s+([\d\.]+)\s*"/i', $contents, $matches)) {
            $width = (float) $matches[1];
            $height = (float) $matches[2];
            if ($width > 0.0 && $height > 0.0) {
                return $width / $height;
            }
        }

        if (
            preg_match('/<svg[^>]*\swidth\s*=\s*"([^"]+)"/i', $contents, $widthMatch)
            && preg_match('/<svg[^>]*\sheight\s*=\s*"([^"]+)"/i', $contents, $heightMatch)
        ) {
            $width = self::parseLength($widthMatch[1] ?? '');
            $height = self::parseLength($heightMatch[1] ?? '');
            if ($width > 0.0 && $height > 0.0) {
                return $width / $height;
            }
        }

        return 1.0;
    }

    private static function parseLength(string $raw): float
    {
        if (preg_match('/([-\d\.]+)/', $raw, $matches)) {
            return (float) $matches[1];
        }

        return 0.0;
    }
}
