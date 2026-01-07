<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\SpacerElementConfig;

final class SpacerElement implements Element
{
    private function __construct(
        private SpacerElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self(SpacerElementConfig::make());
    }

    public function config(SpacerElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getConfig(): SpacerElementConfig
    {
        return $this->config;
    }
}
