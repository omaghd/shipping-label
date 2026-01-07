<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Enums;

enum ImageFit: string
{
    case Contain = 'contain';
    case Cover = 'cover';
}
