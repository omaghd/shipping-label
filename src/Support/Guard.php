<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Support;

final class Guard
{
    public static function nonNegative(float $value): float
    {
        return max(0.0, $value);
    }
}
