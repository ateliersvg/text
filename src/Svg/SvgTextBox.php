<?php

declare(strict_types=1);

namespace Atelier\Text\Svg;

use Alto\Font\Font;

/**
 * @internal computed SVG viewport for one outlined text run
 */
final readonly class SvgTextBox
{
    public function __construct(
        public float $width,
        public float $height,
        public float $padding,
        public float $baselineY,
    ) {
    }

    public static function fromFont(Font $font, float $size, float $advanceWidth = 0.0): self
    {
        $face = $font->face();
        $scale = $size / $face->unitsPerEm;
        $ascent = $face->ascender * $scale;
        $descent = abs($face->descender) * $scale;
        $padding = $size * 0.15;
        $baselineY = $ascent + $padding;
        $width = max($advanceWidth + $padding * 2, $padding * 2);
        $height = $ascent + $descent + $padding * 2;

        return new self($width, $height, $padding, $baselineY);
    }

    public function withAdvanceWidth(float $advanceWidth): self
    {
        return new self(
            width: max($advanceWidth + $this->padding * 2, $this->padding * 2),
            height: $this->height,
            padding: $this->padding,
            baselineY: $this->baselineY,
        );
    }
}
