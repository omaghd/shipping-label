<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use OmaghD\ShippingLabel\Enums\VerticalAlign;
use OmaghD\ShippingLabel\ValueObjects\Padding;

final class ColumnConfig
{
    private function __construct(
        private float $span,
        private Padding $padding,
        private HorizontalAlign $horizontalAlign,
        private VerticalAlign $verticalAlign
    ) {}

    public static function make(): self
    {
        return new self(1.0, Padding::uniform(0.0), HorizontalAlign::Start, VerticalAlign::Top);
    }

    public function span(float $weight): self
    {
        $clone = clone $this;
        $clone->span = $weight;

        return $clone;
    }

    public function padding(Padding $padding): self
    {
        $clone = clone $this;
        $clone->padding = $padding;

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

    public function getSpan(): float
    {
        return $this->span;
    }

    public function getPadding(): Padding
    {
        return $this->padding;
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
