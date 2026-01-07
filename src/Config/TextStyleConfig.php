<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config;

use OmaghD\ShippingLabel\ValueObjects\Color;

final class TextStyleConfig
{
    private function __construct(
        private float $fontSize,
        private Color $color,
        private float $lineHeight,
        private bool $bold
    ) {}

    public static function make(): self
    {
        return new self(8.0, Color::fromHex(Color::BLACK), 0.0, false);
    }

    public function fontSize(float $value): self
    {
        $clone = clone $this;
        $clone->fontSize = $value;

        return $clone;
    }

    public function color(Color|string $hexCode): self
    {
        $clone = clone $this;
        $clone->color = $hexCode instanceof Color ? $hexCode : Color::fromHex($hexCode);

        return $clone;
    }

    public function lineHeight(float $value): self
    {
        $clone = clone $this;
        $clone->lineHeight = $value;

        return $clone;
    }

    public function bold(): self
    {
        $clone = clone $this;
        $clone->bold = true;

        return $clone;
    }

    public function isBold(): bool
    {
        return $this->bold;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }
}
