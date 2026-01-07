<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use OmaghD\ShippingLabel\Enums\ImageFit;

final class ImageElementConfig
{
    private function __construct(
        private float $width,
        private float $height,
        private ImageFit $fit,
        private ?HorizontalAlign $horizontalAlign
    ) {}

    public static function make(): self
    {
        return new self(0.0, 0.0, ImageFit::Contain, null);
    }

    public function width(float $value): self
    {
        $clone = clone $this;
        $clone->width = $value;

        return $clone;
    }

    public function height(float $value): self
    {
        $clone = clone $this;
        $clone->height = $value;

        return $clone;
    }

    public function fitContain(): self
    {
        $clone = clone $this;
        $clone->fit = ImageFit::Contain;

        return $clone;
    }

    public function fitCover(): self
    {
        $clone = clone $this;
        $clone->fit = ImageFit::Cover;

        return $clone;
    }

    public function horizontalAlignStart(): self
    {
        $clone = clone $this;
        $clone->horizontalAlign = HorizontalAlign::Start;

        return $clone;
    }

    public function horizontalAlignCenter(): self
    {
        $clone = clone $this;
        $clone->horizontalAlign = HorizontalAlign::Center;

        return $clone;
    }

    public function horizontalAlignEnd(): self
    {
        $clone = clone $this;
        $clone->horizontalAlign = HorizontalAlign::End;

        return $clone;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getFit(): ImageFit
    {
        return $this->fit;
    }

    public function getHorizontalAlign(): ?HorizontalAlign
    {
        return $this->horizontalAlign;
    }
}
