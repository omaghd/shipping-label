<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\ValueObjects;

final readonly class Color
{
    public const BLACK = '#000000';

    public function __construct(
        public int $red,
        public int $green,
        public int $blue
    ) {}

    public static function fromHex(string $hexCode): self
    {
        $hex = ltrim($hexCode, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));

        return new self($red, $green, $blue);
    }

    public function toRgbArray(): array
    {
        return [$this->red, $this->green, $this->blue];
    }
}
