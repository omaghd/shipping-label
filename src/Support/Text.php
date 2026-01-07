<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Support;

final class Text
{
    public static function normalizeWhitespace(string $text): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        $normalized = preg_replace('/\s*\n\s*/u', ' ', $normalized) ?? $normalized;

        return trim($normalized);
    }
}
