<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

final readonly class TcpdfBox
{
    public function __construct(
        public float $x,
        public float $y,
        public float $width,
        public float $height
    ) {}

    public static function inset(self $box, float $top, float $right, float $bottom, float $left): self
    {
        $x = $box->x + $left;
        $y = $box->y + $top;
        $width = max(0.0, $box->width - ($left + $right));
        $height = max(0.0, $box->height - ($top + $bottom));

        return new self($x, $y, $width, $height);
    }
}
