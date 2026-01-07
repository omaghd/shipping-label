<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\QrCodeElementConfig;

final class QrCodeElement implements Element
{
    private function __construct(
        private string $content,
        private QrCodeElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self('', QrCodeElementConfig::make());
    }

    public function content(string $text): self
    {
        $clone = clone $this;
        $clone->content = $text;

        return $clone;
    }

    public function config(QrCodeElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getConfig(): QrCodeElementConfig
    {
        return $this->config;
    }
}
