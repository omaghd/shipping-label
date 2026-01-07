<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

final class QrCodeElementConfig
{
    private function __construct(
        private float $size,
        private float $fontSize,
        private string $errorCorrection
    ) {}

    public static function make(): self
    {
        return new self(0.0, 0.0, 'H');
    }

    public function size(float $value): self
    {
        $clone = clone $this;
        $clone->size = $value;

        return $clone;
    }

    public function fontSize(float $value): self
    {
        $clone = clone $this;
        $clone->fontSize = $value;

        return $clone;
    }

    public function errorCorrection(string $value): self
    {
        $clone = clone $this;
        $clone->errorCorrection = $value;

        return $clone;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getErrorCorrection(): string
    {
        return $this->errorCorrection;
    }
}
