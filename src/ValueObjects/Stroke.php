<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\ValueObjects;

final readonly class Stroke
{
    public function __construct(
        public float $width
    ) {}
}
