<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Layout;

use OmaghD\ShippingLabel\ValueObjects\Gutter;
use OmaghD\ShippingLabel\ValueObjects\Margin;

final class LabelCursor
{
    private int $index = 0;

    public function __construct(
        private readonly int $columns,
        private readonly int $rows,
        private readonly float $pageWidth,
        private readonly float $pageHeight,
        private readonly Margin $margin,
        private readonly Gutter $gutter
    ) {}

    public function current(): LayoutContext
    {
        $column = $this->index % $this->columns;
        $row = intdiv($this->index, $this->columns);

        $width = $this->slotWidth();
        $height = $this->slotHeight();

        $x = $this->margin->left + ($column * ($width + $this->gutter->horizontal));
        $y = $this->margin->top + ($row * ($height + $this->gutter->vertical));

        return new LayoutContext($x, $y, $width, $height);
    }

    public function nextLabel(): bool
    {
        $this->index++;
        if ($this->index >= ($this->columns * $this->rows)) {
            $this->index = 0;

            return true;
        }

        return false;
    }

    private function slotWidth(): float
    {
        $available = $this->pageWidth - ($this->margin->left + $this->margin->right);
        $available -= max(0, $this->columns - 1) * $this->gutter->horizontal;

        return $available / $this->columns;
    }

    private function slotHeight(): float
    {
        $available = $this->pageHeight - ($this->margin->top + $this->margin->bottom);
        $available -= max(0, $this->rows - 1) * $this->gutter->vertical;

        return $available / $this->rows;
    }
}
