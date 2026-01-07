<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Layout;

final readonly class LayoutContext
{
    public function __construct(
        public float $x,
        public float $y,
        public float $width,
        public float $height
    ) {}
}
