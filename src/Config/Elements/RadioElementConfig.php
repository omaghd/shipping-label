<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use OmaghD\ShippingLabel\Enums\VerticalAlign;

final class RadioElementConfig
{
    private function __construct(
        private bool $rtl,
        private float $boxSize,
        private float $fontSize,
        private float $lineHeight,
        private HorizontalAlign $horizontalAlign,
        private VerticalAlign $verticalAlign
    ) {}

    public static function make(): self
    {
        return new self(false, 2.5, 0.0, 0.0, HorizontalAlign::Start, VerticalAlign::Middle);
    }

    public function rtl(): self
    {
        $clone = clone $this;
        $clone->rtl = true;

        return $clone;
    }

    public function boxSize(float $value): self
    {
        $clone = clone $this;
        $clone->boxSize = $value;

        return $clone;
    }

    public function fontSize(float $value): self
    {
        $clone = clone $this;
        $clone->fontSize = $value;

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

    public function verticalAlignTop(): self
    {
        $clone = clone $this;
        $clone->verticalAlign = VerticalAlign::Top;

        return $clone;
    }

    public function verticalAlignMiddle(): self
    {
        $clone = clone $this;
        $clone->verticalAlign = VerticalAlign::Middle;

        return $clone;
    }

    public function verticalAlignBottom(): self
    {
        $clone = clone $this;
        $clone->verticalAlign = VerticalAlign::Bottom;

        return $clone;
    }

    public function isRtl(): bool
    {
        return $this->rtl;
    }

    public function getBoxSize(): float
    {
        return $this->boxSize;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }

    public function getHorizontalAlign(): HorizontalAlign
    {
        return $this->horizontalAlign;
    }

    public function getVerticalAlign(): VerticalAlign
    {
        return $this->verticalAlign;
    }
}
