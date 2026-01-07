<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

final class SpacerElementConfig
{
    private function __construct(
        private float $height
    ) {}

    public static function make(): self
    {
        return new self(0.0);
    }

    public function height(float $value): self
    {
        $clone = clone $this;
        $clone->height = $value;

        return $clone;
    }

    public function getHeight(): float
    {
        return $this->height;
    }
}
