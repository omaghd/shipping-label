<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Nodes;

use OmaghD\ShippingLabel\Config\ColumnConfig;
use OmaghD\ShippingLabel\Elements\Element;

final class Column
{
    private array $elements = [];

    private function __construct(
        private ColumnConfig $config
    ) {}

    public static function make(): self
    {
        return new self(ColumnConfig::make());
    }

    public function config(ColumnConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function addElement(Element $element): self
    {
        $clone = clone $this;
        $clone->elements[] = $element;

        return $clone;
    }

    public function getConfig(): ColumnConfig
    {
        return $this->config;
    }

    public function getElements(): array
    {
        return $this->elements;
    }
}
