<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\ValueObjects;

final readonly class Gutter
{
    public function __construct(
        public float $horizontal,
        public float $vertical
    ) {}

    public static function symmetric(float $value): self
    {
        return new self($value, $value);
    }
}
