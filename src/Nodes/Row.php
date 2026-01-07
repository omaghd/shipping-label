<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Nodes;

use OmaghD\ShippingLabel\Config\RowConfig;

final class Row
{
    private array $columns = [];

    private function __construct(
        private RowConfig $config
    ) {}

    public static function make(): self
    {
        return new self(RowConfig::make());
    }

    public function config(RowConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function addColumn(Column $column): self
    {
        $clone = clone $this;
        $clone->columns[] = $column;

        return $clone;
    }

    public function getConfig(): RowConfig
    {
        return $this->config;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
