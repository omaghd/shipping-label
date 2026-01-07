<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel;

use OmaghD\ShippingLabel\Config\LabelConfig;
use OmaghD\ShippingLabel\Enums\PdfRenderMode;
use OmaghD\ShippingLabel\Layout\LabelCursor;
use OmaghD\ShippingLabel\Nodes\Row;
use OmaghD\ShippingLabel\Tcpdf\TcpdfRenderer;

final class ShippingLabel
{
    private LabelConfig $config;

    private array $labels = [[]];

    private int $currentIndex = 0;

    private function __construct()
    {
        $this->config = LabelConfig::make();
    }

    public static function make(): self
    {
        return new self;
    }

    public function config(LabelConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function addRow(Row $row): self
    {
        $this->labels[$this->currentIndex][] = $row;

        return $this;
    }

    public function nextLabel(): self
    {
        $this->currentIndex++;
        if (! array_key_exists($this->currentIndex, $this->labels)) {
            $this->labels[$this->currentIndex] = [];
        }

        return $this;
    }

    public function render(string $filename = 'label.pdf', PdfRenderMode $renderMode = PdfRenderMode::INLINE): string
    {
        $renderer = new TcpdfRenderer;

        return $renderer->render($this->config, $this->labels, $filename, $renderMode);
    }

    public function cursor(): LabelCursor
    {
        return new LabelCursor(
            $this->config->getColumns(),
            $this->config->getRows(),
            $this->config->getPageWidth(),
            $this->config->getPageHeight(),
            $this->config->getMargin(),
            $this->config->getGutter()
        );
    }
}
