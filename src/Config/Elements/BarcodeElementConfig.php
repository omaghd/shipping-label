<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

final class BarcodeElementConfig
{
    private function __construct(
        private float $height,
        private float $width
    ) {}

    public static function make(): self
    {
        return new self(0.0, 0.0);
    }

    public function height(float $value): self
    {
        $clone = clone $this;
        $clone->height = $value;

        return $clone;
    }

    public function width(float $value): self
    {
        $clone = clone $this;
        $clone->width = $value;

        return $clone;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWidth(): float
    {
        return $this->width;
    }
}
