<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config;

final class RowConfig
{
    private function __construct(
        private float $heightRatio
    ) {}

    public static function make(): self
    {
        return new self(1.0);
    }

    public function height(float $ratio): self
    {
        $clone = clone $this;
        $clone->heightRatio = $ratio;

        return $clone;
    }

    public function getHeightRatio(): float
    {
        return $this->heightRatio;
    }
}
