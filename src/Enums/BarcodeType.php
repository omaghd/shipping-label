<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Enums;

enum BarcodeType: string
{
    case C39 = 'C39';
    case C128 = 'C128';
}
