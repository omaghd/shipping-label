<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\BulletsElementConfig;

final class BulletsElement implements Element
{
    private array $items = [];

    private function __construct(
        private BulletsElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self(BulletsElementConfig::make());
    }

    public function addItem(string $text): self
    {
        $clone = clone $this;
        $clone->items[] = $text;

        return $clone;
    }

    public function config(BulletsElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getConfig(): BulletsElementConfig
    {
        return $this->config;
    }
}
