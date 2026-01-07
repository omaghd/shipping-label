<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Layout;

use OmaghD\ShippingLabel\Nodes\Row;
use OmaghD\ShippingLabel\Support\Guard;

final readonly class LayoutEngine
{
    public function __construct(
        private LayoutContext $context
    ) {}

    public function rows(array $rows): array
    {
        $totalRatio = 0.0;
        foreach ($rows as $row) {
            if (! $row instanceof Row) {
                continue;
            }
            $totalRatio += Guard::nonNegative($row->getConfig()->getHeightRatio());
        }
        if ($totalRatio <= 0.0) {
            $totalRatio = 1.0;
        }

        $layouts = [];
        $currentY = $this->context->y;
        foreach ($rows as $row) {
            if (! $row instanceof Row) {
                continue;
            }
            $ratio = Guard::nonNegative($row->getConfig()->getHeightRatio());
            $height = ($this->context->height * $ratio) / $totalRatio;
            $columns = $this->columns($row, $currentY, $height);
            $layouts[] = new RowLayout($currentY, $height, $columns);
            $currentY += $height;
        }

        return $layouts;
    }

    private function columns(Row $row, float $rowY, float $rowHeight): array
    {
        $columns = $row->getColumns();
        $totalWeight = 0.0;
        foreach ($columns as $column) {
            $totalWeight += Guard::nonNegative($column->getConfig()->getSpan());
        }
        if ($totalWeight <= 0.0) {
            $totalWeight = 1.0;
        }

        $layouts = [];
        $currentX = $this->context->x;
        foreach ($columns as $column) {
            $weight = Guard::nonNegative($column->getConfig()->getSpan());
            $width = ($this->context->width * $weight) / $totalWeight;
            $layouts[] = new ColumnLayout($currentX, $rowY, $width, $rowHeight);
            $currentX += $width;
        }

        return $layouts;
    }
}
