<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\ImageElementConfig;

final class ImageElement implements Element
{
    private function __construct(
        private string $path,
        private ImageElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self('', ImageElementConfig::make());
    }

    public function path(string $path): self
    {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function config(ImageElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getConfig(): ImageElementConfig
    {
        return $this->config;
    }
}
