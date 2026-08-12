<?php

declare(strict_types=1);

namespace Atelier\Text\Shaping;

final readonly class GlyphRun
{
    /**
     * @param list<PositionedGlyph> $glyphs
     */
    public function __construct(
        public array $glyphs,
        public TextDirection $direction = TextDirection::LeftToRight,
    ) {
    }
}
