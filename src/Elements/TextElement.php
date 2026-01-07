<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\TextElementConfig;

final class TextElement implements Element
{
    private function __construct(
        private string $text,
        private TextElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self('', TextElementConfig::make());
    }

    public function content(string $text): self
    {
        $clone = clone $this;
        $clone->text = $text;

        return $clone;
    }

    public function config(TextElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getConfig(): TextElementConfig
    {
        return $this->config;
    }
}
