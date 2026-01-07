<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Enums;

enum PdfRenderMode: string
{
    case INLINE = 'inline';
    case DOWNLOAD = 'download';
    case STRING = 'string';
}
