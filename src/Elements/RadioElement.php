<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Elements;

use OmaghD\ShippingLabel\Config\Elements\RadioElementConfig;

final class RadioElement implements Element
{
    private function __construct(
        private string $label,
        private string $selectedLabel,
        private string $unselectedLabel,
        private bool $selected,
        private RadioElementConfig $config
    ) {}

    public static function make(): self
    {
        return new self('', '', '', false, RadioElementConfig::make());
    }

    public function label(string $text): self
    {
        $clone = clone $this;
        $clone->label = $text;

        return $clone;
    }

    public function selectedLabel(string $text): self
    {
        $clone = clone $this;
        $clone->selectedLabel = $text;

        return $clone;
    }

    public function unselectedLabel(string $text): self
    {
        $clone = clone $this;
        $clone->unselectedLabel = $text;

        return $clone;
    }

    public function selected(bool $state): self
    {
        $clone = clone $this;
        $clone->selected = $state;

        return $clone;
    }

    public function config(RadioElementConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;

        return $clone;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSelectedLabel(): string
    {
        return $this->selectedLabel;
    }

    public function getUnselectedLabel(): string
    {
        return $this->unselectedLabel;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function getConfig(): RadioElementConfig
    {
        return $this->config;
    }
}
