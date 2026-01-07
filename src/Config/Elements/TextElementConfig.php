<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;

final class TextElementConfig
{
    private function __construct(
        private bool $rtl,
        private bool $bold,
        private float $fontSize,
        private int $maxLines,
        private float $lineHeight,
        private ?HorizontalAlign $horizontalAlign
    ) {}

    public static function make(): self
    {
        return new self(false, false, 0.0, 0, 0.0, null);
    }

    public function rtl(): self
    {
        $clone = clone $this;
        $clone->rtl = true;

        return $clone;
    }

    public function bold(): self
    {
        $clone = clone $this;
        $clone->bold = true;

        return $clone;
    }

    public function fontSize(float $value): self
    {
        $clone = clone $this;
        $clone->fontSize = $value;

        return $clone;
    }

    public function maxLines(int $value): self
    {
        $clone = clone $this;
        $clone->maxLines = $value;

        return $clone;
    }

    public function lineHeight(float $value): self
    {
        $clone = clone $this;
        $clone->lineHeight = $value;

        return $clone;
    }

    public function horizontalAlignStart(): self
    {
        $clone = clone $this;
        $clone->horizontalAlign = HorizontalAlign::Start;

        return $clone;
    }

    public function horizontalAlignCenter(): self
    {
        $clone = clone $this;
        $clone->horizontalAlign = HorizontalAlign::Center;

        return $clone;
    }

    public function horizontalAlignEnd(): self
    {
        $clone = clone $this;
        $clone->horizontalAlign = HorizontalAlign::End;

        return $clone;
    }

    public function isRtl(): bool
    {
        return $this->rtl;
    }

    public function isBold(): bool
    {
        return $this->bold;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getMaxLines(): int
    {
        return $this->maxLines;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }

    public function getHorizontalAlign(): ?HorizontalAlign
    {
        return $this->horizontalAlign;
    }
}
