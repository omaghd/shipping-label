<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel\Tcpdf;

use OmaghD\ShippingLabel\Config\LabelConfig;
use OmaghD\ShippingLabel\Elements\BarcodeElement;
use OmaghD\ShippingLabel\Elements\BulletsElement;
use OmaghD\ShippingLabel\Elements\ImageElement;
use OmaghD\ShippingLabel\Elements\QrCodeElement;
use OmaghD\ShippingLabel\Elements\RadioElement;
use OmaghD\ShippingLabel\Elements\SpacerElement;
use OmaghD\ShippingLabel\Elements\TextElement;
use OmaghD\ShippingLabel\Enums\HorizontalAlign;
use OmaghD\ShippingLabel\Enums\PdfRenderMode;
use OmaghD\ShippingLabel\Enums\VerticalAlign;
use OmaghD\ShippingLabel\Layout\LabelCursor;
use OmaghD\ShippingLabel\Layout\LayoutContext;
use OmaghD\ShippingLabel\Layout\LayoutEngine;
use OmaghD\ShippingLabel\Nodes\Column;
use OmaghD\ShippingLabel\Nodes\Row;
use OmaghD\ShippingLabel\Support\Text;
use TCPDF;

final class TcpdfRenderer
{
    private const PT_TO_MM = 0.3527777778;

    public function render(LabelConfig $config, array $labels, string $filename, PdfRenderMode $renderMode): string
    {
        $factory = new TcpdfFactory;
        $pdf = $factory->make($config);
        $pdf->AddPage();
        $cursor = new LabelCursor(
            $config->getColumns(),
            $config->getRows(),
            $pdf->getPageWidth(),
            $pdf->getPageHeight(),
            $config->getMargin(),
            $config->getGutter()
        );

        foreach ($labels as $labelIndex => $rows) {
            if ($labelIndex > 0 && $cursor->nextLabel()) {
                $pdf->AddPage();
            }

            $context = $cursor->current();
            $this->drawLabelBorder($pdf, $config, $context);
            $this->renderRows($pdf, $config, $context, $rows);
        }

        return $pdf->Output($filename, $this->resolveRenderMode($renderMode));
    }

    private function drawLabelBorder(TCPDF $pdf, LabelConfig $config, LayoutContext $context): void
    {
        $stroke = $config->getBorderStroke()->width;
        if ($stroke <= 0.0) {
            return;
        }

        $pdf->SetLineWidth($stroke);
        $color = $config->getBorderColor()->toRgbArray();
        $pdf->SetDrawColor($color[0], $color[1], $color[2]);
        $pdf->Rect($context->x, $context->y, $context->width, $context->height);
    }

    private function renderRows(TCPDF $pdf, LabelConfig $config, LayoutContext $context, array $rows): void
    {
        $engine = new LayoutEngine($context);
        $rowLayouts = $engine->rows($rows);
        $this->drawGridLines($pdf, $config, $context, $rowLayouts);

        foreach ($rowLayouts as $rowIndex => $rowLayout) {
            $row = $rows[$rowIndex] ?? null;
            if (! $row instanceof Row) {
                continue;
            }

            $columns = $row->getColumns();
            foreach ($rowLayout->columns as $columnIndex => $columnLayout) {
                $column = $columns[$columnIndex] ?? null;
                if (! $column instanceof Column) {
                    continue;
                }

                $this->renderColumn($pdf, $config, $column, $columnLayout->x, $columnLayout->y, $columnLayout->width, $columnLayout->height);
            }
        }
    }

    private function drawGridLines(TCPDF $pdf, LabelConfig $config, LayoutContext $context, array $rowLayouts): void
    {
        $stroke = $config->getBorderStroke()->width;
        if ($stroke <= 0.0) {
            return;
        }

        $color = $config->getBorderColor()->toRgbArray();
        $pdf->SetLineWidth($stroke);
        $pdf->SetDrawColor($color[0], $color[1], $color[2]);

        $rowsCount = count($rowLayouts);
        for ($rowIndex = 0; $rowIndex < $rowsCount; $rowIndex++) {
            $rowLayout = $rowLayouts[$rowIndex];
            if ($rowIndex > 0) {
                $pdf->Line($context->x, $rowLayout->y, $context->x + $context->width, $rowLayout->y);
            }

            $columns = $rowLayout->columns;
            $columnsCount = count($columns);
            for ($columnIndex = 0; $columnIndex < $columnsCount - 1; $columnIndex++) {
                $columnLayout = $columns[$columnIndex];
                $x = $columnLayout->x + $columnLayout->width;
                $pdf->Line($x, $rowLayout->y, $x, $rowLayout->y + $rowLayout->height);
            }
        }
    }

    private function renderColumn(
        TCPDF $pdf,
        LabelConfig $config,
        Column $column,
        float $x,
        float $y,
        float $width,
        float $height
    ): void {
        $padding = $column->getConfig()->getPadding();
        $contentX = $x + $padding->left;
        $contentY = $y + $padding->top;
        $contentWidth = max(0.0, $width - ($padding->left + $padding->right));
        $contentHeight = max(0.0, $height - ($padding->top + $padding->bottom));

        if ($contentWidth <= 0.0 || $contentHeight <= 0.0) {
            return;
        }

        $elements = $column->getElements();
        $layouts = [];
        $totalHeight = 0.0;

        foreach ($elements as $element) {
            $layout = $this->buildElementLayout($pdf, $config, $column, $element, $contentWidth);
            if ($layout === null) {
                continue;
            }
            $layouts[] = $layout;
            $totalHeight += $layout['height'];
        }

        $startY = match ($column->getConfig()->getVerticalAlign()) {
            VerticalAlign::Bottom => $contentY + max(0.0, $contentHeight - $totalHeight),
            VerticalAlign::Middle => $contentY + max(0.0, ($contentHeight - $totalHeight) / 2),
            default => $contentY,
        };

        $pdf->StartTransform();
        $pdf->Rect($contentX, $contentY, $contentWidth, $contentHeight, 'CNZ');

        $cursorY = $startY;
        foreach ($layouts as $layout) {
            if (($cursorY + $layout['height']) > ($contentY + $contentHeight + 0.001)) {
                break;
            }
            $layout['render']($contentX, $cursorY, $contentWidth, $layout['height']);
            $cursorY += $layout['height'];
        }

        $pdf->StopTransform();
    }

    private function buildElementLayout(TCPDF $pdf, LabelConfig $config, Column $column, object $element, float $contentWidth): ?array
    {
        if ($element instanceof SpacerElement) {
            $height = max(0.0, $element->getConfig()->getHeight());

            return [
                'height' => $height,
                'render' => static function (): void {},
            ];
        }

        if ($element instanceof TextElement) {
            $style = $config->getDefaultTextStyle();
            $configElement = $element->getConfig();
            $fontSize = $configElement->getFontSize() > 0.0 ? $configElement->getFontSize() : $style->getFontSize();
            $lineHeight = $configElement->getLineHeight() > 0.0 ? $configElement->getLineHeight() : $this->resolveLineHeight($style->getLineHeight(), $fontSize);
            $maxLines = $configElement->getMaxLines() > 0 ? $configElement->getMaxLines() : 9999;
            $text = Text::normalizeWhitespace($element->getText());
            $bold = $style->isBold() || $configElement->isBold();
            $runs = TcpdfTextRuns::fromMarkdown($text, TcpdfFonts::DEFAULT_FONT, $fontSize, $bold);
            $lines = TcpdfTextRuns::wrapRunsToLines($pdf, $runs, $contentWidth, $maxLines, '…');
            $height = $lineHeight * count($lines);
            $horizontalAlign = $configElement->getHorizontalAlign() ?? $column->getConfig()->getHorizontalAlign();
            $rtl = $configElement->isRtl();

            return [
                'height' => $height,
                'render' => function (float $x, float $y, float $width, float $height) use ($pdf, $lines, $lineHeight, $horizontalAlign, $rtl, $style): void {
                    $pdf->SetTextColor($style->getColor()->red, $style->getColor()->green, $style->getColor()->blue);
                    $cursorY = $y;
                    foreach ($lines as $lineRuns) {
                        if (($cursorY + $lineHeight) > ($y + $height + 0.001)) {
                            break;
                        }
                        $this->renderRuns($pdf, $x, $cursorY, $width, $lineRuns, $horizontalAlign, $rtl);
                        $cursorY += $lineHeight;
                    }
                },
            ];
        }

        if ($element instanceof BulletsElement) {
            $configElement = $element->getConfig();
            $style = $config->getDefaultTextStyle();
            $fontSize = $configElement->getFontSize() > 0.0 ? $configElement->getFontSize() : $style->getFontSize();
            $lineHeight = $configElement->getLineHeight() > 0.0 ? $configElement->getLineHeight() : $this->resolveLineHeight($style->getLineHeight(), $fontSize);
            $horizontalAlign = $configElement->getHorizontalAlign() ?? $column->getConfig()->getHorizontalAlign();
            $rtl = $configElement->isRtl();

            $builder = new TcpdfBullets;
            $lines = $builder->buildLines(
                $pdf,
                $element->getItems(),
                TcpdfFonts::DEFAULT_FONT,
                $fontSize,
                $lineHeight,
                $configElement->getBullet(),
                $contentWidth,
                1,
                '…',
                $style->isBold()
            );

            $height = $lineHeight * count($lines);

            return [
                'height' => $height,
                'render' => function (float $x, float $y, float $width, float $height) use ($pdf, $lines, $lineHeight, $horizontalAlign, $rtl, $fontSize): void {
                    $cursorY = $y;
                    foreach ($lines as $line) {
                        if (($cursorY + $lineHeight) > ($y + $height + 0.001)) {
                            break;
                        }
                        $this->renderBulletLine($pdf, $x, $cursorY, $width, $line, $horizontalAlign, $rtl, $fontSize);
                        $cursorY += $lineHeight;
                    }
                },
            ];
        }

        if ($element instanceof ImageElement) {
            $configElement = $element->getConfig();
            $height = $configElement->getHeight() > 0.0 ? $configElement->getHeight() : $contentWidth;
            $horizontalAlign = $configElement->getHorizontalAlign() ?? $column->getConfig()->getHorizontalAlign();

            return [
                'height' => $height,
                'render' => function (float $x, float $y, float $width, float $height) use ($pdf, $element, $configElement, $horizontalAlign): void {
                    $image = new TcpdfImage;
                    $image->draw(
                        $pdf,
                        $element->getPath(),
                        $x,
                        $y,
                        $width,
                        $height,
                        $configElement->getWidth(),
                        $configElement->getHeight(),
                        $configElement->getFit(),
                        $horizontalAlign
                    );
                },
            ];
        }

        if ($element instanceof BarcodeElement) {
            $configElement = $element->getConfig();
            $height = max(0.0, $configElement->getHeight());
            $horizontalAlign = $column->getConfig()->getHorizontalAlign();

            return [
                'height' => $height,
                'render' => function (float $x, float $y, float $width, float $height) use ($pdf, $element, $configElement, $horizontalAlign): void {
                    $barcode = new TcpdfBarcode;
                    $barcode->draw(
                        $pdf,
                        $element->getCode(),
                        $element->getType()->value,
                        $x,
                        $y,
                        $width,
                        $height,
                        $horizontalAlign,
                        $configElement->getWidth()
                    );
                },
            ];
        }

        if ($element instanceof QrCodeElement) {
            $configElement = $element->getConfig();
            $height = max(0.0, $configElement->getSize());
            $horizontalAlign = $column->getConfig()->getHorizontalAlign();

            return [
                'height' => $height,
                'render' => function (float $x, float $y, float $width, float $height) use ($pdf, $element, $configElement, $horizontalAlign): void {
                    $qr = new TcpdfQrCode;
                    $qr->draw(
                        $pdf,
                        $element->getContent(),
                        $configElement->getErrorCorrection(),
                        $x,
                        $y,
                        $width,
                        $height,
                        $horizontalAlign,
                        $configElement->getSize()
                    );
                },
            ];
        }

        if ($element instanceof RadioElement) {
            $configElement = $element->getConfig();
            $fontSize = $configElement->getFontSize() > 0.0 ? $configElement->getFontSize() : $config->getDefaultTextStyle()->getFontSize();
            $lineHeight = $configElement->getLineHeight() > 0.0 ? $configElement->getLineHeight() : $this->resolveLineHeight($config->getDefaultTextStyle()->getLineHeight(), $fontSize);
            $height = $lineHeight;

            return [
                'height' => $height,
                'render' => function (float $x, float $y, float $width, float $height) use ($pdf, $element, $configElement, $fontSize, $lineHeight): void {
                    $radio = new TcpdfRadio;
                    $radio->draw(
                        $pdf,
                        $element->getLabel(),
                        $element->getSelectedLabel(),
                        $element->getUnselectedLabel(),
                        $element->isSelected(),
                        $x,
                        $y,
                        $width,
                        $height,
                        $configElement->getBoxSize(),
                        $fontSize,
                        $lineHeight,
                        $configElement->isRtl(),
                        $configElement->getHorizontalAlign(),
                        $configElement->getVerticalAlign()
                    );
                },
            ];
        }

        return null;
    }

    private function resolveLineHeight(float $lineHeight, float $fontSize): float
    {
        if ($lineHeight > 0.0) {
            return $lineHeight;
        }

        return ($fontSize * self::PT_TO_MM) * 1.2;
    }

    private function resolveHorizontalAlign(HorizontalAlign $align, bool $rtl): string
    {
        return match ($align) {
            HorizontalAlign::Center => 'C',
            HorizontalAlign::End => $rtl ? 'L' : 'R',
            default => $rtl ? 'R' : 'L',
        };
    }

    private function renderRuns(TCPDF $pdf, float $x, float $y, float $width, array $runs, HorizontalAlign $align, bool $rtl): void
    {
        $runs = TcpdfTextRuns::normalize($runs);
        if ($runs === []) {
            return;
        }

        $lineWidth = TcpdfTextRuns::measureRunsWidth($pdf, $runs);
        $alignment = $this->resolveHorizontalAlign($align, $rtl);
        $startX = match ($alignment) {
            'R' => $x + max(0.0, $width - $lineWidth),
            'C' => $x + max(0.0, ($width - $lineWidth) / 2),
            default => $x,
        };

        if ($rtl) {
            $cursorX = $startX + $lineWidth;

            foreach ($runs as $run) {
                $pdf->SetFont($run->font, $run->style, $run->fontSize);

                $runWidth = $pdf->GetStringWidth($run->text);

                $cursorX -= $runWidth;

                $pdf->Text($cursorX, $y, $run->text);
            }

            return;
        }

        $cursorX = $startX;

        foreach ($runs as $run) {
            $pdf->SetFont($run->font, $run->style, $run->fontSize);
            $pdf->Text($cursorX, $y, $run->text);

            $cursorX += $pdf->GetStringWidth($run->text);
        }
    }

    private function renderBulletLine(
        TCPDF $pdf,
        float $x,
        float $y,
        float $width,
        BulletLine $line,
        HorizontalAlign $align,
        bool $rtl,
        float $fontSize
    ): void {
        $bullet = $line->bullet;
        $bulletWidth = $bullet === '' ? 0.0 : TcpdfTextRuns::measurePlainWidth($pdf, $bullet, TcpdfFonts::DEFAULT_FONT, '', $fontSize);
        $gap = $bullet === '' ? 0.0 : 1.2;
        $textWidth = max(0.0, $width - ($bulletWidth + $gap));

        $startAlign = $this->resolveHorizontalAlign(HorizontalAlign::Start, $rtl);
        $bulletX = match ($startAlign) {
            'R' => $x + max(0.0, $width - $bulletWidth),
            default => $x,
        };

        if ($bullet !== '') {
            $pdf->SetFont(TcpdfFonts::DEFAULT_FONT, '', $fontSize);
            $pdf->Text($bulletX, $y, $bullet);
        }

        $textX = $startAlign === 'R' ? $x : $x + $bulletWidth + $gap;
        $this->renderRuns($pdf, $textX, $y, $textWidth, $line->runs, $align, $rtl);
    }

    public function resolveRenderMode(PdfRenderMode $renderMode): string
    {
        return match ($renderMode) {
            PdfRenderMode::DOWNLOAD => 'D',
            PdfRenderMode::STRING => 'S',
            default => 'I',
        };
    }
}
