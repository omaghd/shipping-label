<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\BarcodeElementConfig;
use OmaghD\ShippingLabel\Enums\BarcodeType;

final class BarcodeElement implements Element
{
    private function __construct(
        private string $code,
        private BarcodeType $type,
        private BarcodeElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self('', BarcodeType::C128, BarcodeElementConfig::make());
    }

    public function code(string $text): self
    {
        $clone = clone $this;
        $clone->code = $text;

        return $clone;
    }

    public function type(BarcodeType $type): self
    {
        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    public function config(BarcodeElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): BarcodeType
    {
        return $this->type;
    }

    public function getConfig(): BarcodeElementConfig
    {
        return $this->config;
    }
}
