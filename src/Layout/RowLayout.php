<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Layout;

final readonly class RowLayout
{
    public function __construct(
        public float $y,
        public float $height,
        public array $columns
    ) {}
}
