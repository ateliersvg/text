<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Glyph\GlyphOutline;
use Atelier\Text\Shaping\PositionedGlyph;

/**
 * @internal
 */
final readonly class OutlinedGlyph
{
    public function __construct(
        public PositionedGlyph $positionedGlyph,
        public GlyphOutline $outline,
        public string $pathData,
        public float $x,
        public float $baselineY,
    ) {
    }
}
