<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Config;

use OmaghD\ShippingLabel\Enums\Orientation;
use OmaghD\ShippingLabel\Enums\Unit;
use OmaghD\ShippingLabel\ValueObjects\Color;
use OmaghD\ShippingLabel\ValueObjects\Gutter;
use OmaghD\ShippingLabel\ValueObjects\Margin;
use OmaghD\ShippingLabel\ValueObjects\Stroke;

final class LabelConfig
{
    private function __construct(
        private float $pageWidth,
        private float $pageHeight,
        private int $columns,
        private int $rows,
        private Orientation $orientation,
        private Unit $unit,
        private Margin $margin,
        private Gutter $gutter,
        private Stroke $borderStroke,
        private Color $borderColor,
        private TextStyleConfig $defaultTextStyle
    ) {}

    public static function make(): self
    {
        return new self(
            pageWidth: 100.0,
            pageHeight: 100.0,
            columns: 1,
            rows: 1,
            orientation: Orientation::Portrait,
            unit: Unit::Mm,
            margin: Margin::uniform(0.0),
            gutter: Gutter::symmetric(0.0),
            borderStroke: new Stroke(0.0),
            borderColor: Color::fromHex(Color::BLACK),
            defaultTextStyle: TextStyleConfig::make()
        );
    }

    public function square(): self
    {
        $clone = clone $this;
        $clone->pageWidth = 100.0;
        $clone->pageHeight = 100.0;
        $clone->columns = 1;
        $clone->rows = 1;

        return $clone;
    }

    public function a4(): self
    {
        $clone = clone $this;
        $clone->pageWidth = 210.0;
        $clone->pageHeight = 297.0;
        $clone->columns = 2;
        $clone->rows = 2;

        return $clone;
    }

    public function a5(): self
    {
        $clone = clone $this;
        $clone->pageWidth = 210.0;
        $clone->pageHeight = 148.0;
        $clone->orientation = Orientation::Landscape;
        $clone->columns = 2;
        $clone->rows = 1;

        return $clone;
    }

    public function a6(): self
    {
        $clone = clone $this;
        $clone->pageWidth = 105.0;
        $clone->pageHeight = 148.0;
        $clone->columns = 1;
        $clone->rows = 1;

        return $clone;
    }

    public function orientation(Orientation $orientation): self
    {
        $clone = clone $this;
        $clone->orientation = $orientation;

        return $clone;
    }

    public function unit(Unit $unit): self
    {
        $clone = clone $this;
        $clone->unit = $unit;

        return $clone;
    }

    public function margin(Margin $margin): self
    {
        $clone = clone $this;
        $clone->margin = $margin;

        return $clone;
    }

    public function gutter(Gutter $gutter): self
    {
        $clone = clone $this;
        $clone->gutter = $gutter;

        return $clone;
    }

    public function borderStroke(float $width): self
    {
        $clone = clone $this;
        $clone->borderStroke = new Stroke($width);

        return $clone;
    }

    public function borderColor(Color|string $hexCode): self
    {
        $clone = clone $this;
        $clone->borderColor = $hexCode instanceof Color ? $hexCode : Color::fromHex($hexCode);

        return $clone;
    }

    public function defaultTextStyle(TextStyleConfig $config): self
    {
        $clone = clone $this;
        $clone->defaultTextStyle = $config;

        return $clone;
    }

    public function getPageWidth(): float
    {
        return $this->pageWidth;
    }

    public function getPageHeight(): float
    {
        return $this->pageHeight;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getOrientation(): Orientation
    {
        return $this->orientation;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function getMargin(): Margin
    {
        return $this->margin;
    }

    public function getGutter(): Gutter
    {
        return $this->gutter;
    }

    public function getBorderStroke(): Stroke
    {
        return $this->borderStroke;
    }

    public function getBorderColor(): Color
    {
        return $this->borderColor;
    }

    public function getDefaultTextStyle(): TextStyleConfig
    {
        return $this->defaultTextStyle;
    }
}
