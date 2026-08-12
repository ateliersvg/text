<?php

declare(strict_types=1);

namespace Atelier\Text\Shaping;

use Alto\Font\Glyph\GlyphId;
use Alto\Font\Glyph\GlyphMetrics;

final readonly class PositionedGlyph
{
    public function __construct(
        public GlyphId $glyphId,
        public GlyphMetrics $metrics,
        public int $cluster,
        public float $xAdvance,
        public float $yAdvance = 0.0,
        public float $xOffset = 0.0,
        public float $yOffset = 0.0,
        public ?int $codepoint = null,
    ) {
    }
}
