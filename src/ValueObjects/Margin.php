<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\ValueObjects;

final readonly class Margin
{
    public function __construct(
        public float $top,
        public float $right,
        public float $bottom,
        public float $left
    ) {}

    public static function uniform(float $value): self
    {
        return new self($value, $value, $value, $value);
    }

    public static function symmetric(float $vertical, float $horizontal): self
    {
        return new self($vertical, $horizontal, $vertical, $horizontal);
    }

    public function withTop(float $value): self
    {
        return new self($value, $this->right, $this->bottom, $this->left);
    }

    public function withRight(float $value): self
    {
        return new self($this->top, $value, $this->bottom, $this->left);
    }

    public function withBottom(float $value): self
    {
        return new self($this->top, $this->right, $value, $this->left);
    }

    public function withLeft(float $value): self
    {
        return new self($this->top, $this->right, $this->bottom, $value);
    }
}
