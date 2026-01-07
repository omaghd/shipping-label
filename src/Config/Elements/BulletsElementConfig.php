<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config\Elements;

use OmaghD\ShippingLabel\Enums\HorizontalAlign;

final class BulletsElementConfig
{
    private function __construct(
        private bool $rtl,
        private float $fontSize,
        private float $lineHeight,
        private string $bullet,
        private ?HorizontalAlign $horizontalAlign
    ) {}

    public static function make(): self
    {
        return new self(false, 0.0, 0.0, '•', null);
    }

    public function rtl(): self
    {
        $clone = clone $this;
        $clone->rtl = true;

        return $clone;
    }

    public function fontSize(float $value): self
    {
        $clone = clone $this;
        $clone->fontSize = $value;

        return $clone;
    }

    public function lineHeight(float $value): self
    {
        $clone = clone $this;
        $clone->lineHeight = $value;

        return $clone;
    }

    public function bullet(string $value): self
    {
        $clone = clone $this;
        $clone->bullet = $value;

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

    public function isRtl(): bool
    {
        return $this->rtl;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }

    public function getBullet(): string
    {
        return $this->bullet;
    }

    public function getHorizontalAlign(): ?HorizontalAlign
    {
        return $this->horizontalAlign;
    }
}
