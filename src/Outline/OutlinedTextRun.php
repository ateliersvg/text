<?php

declare(strict_types=1);

namespace Atelier\Text\Outline;

use Atelier\Text\OutlinedGlyph;

/**
 * @internal text-run geometry before choosing SVG path-only or SVG document output
 */
final readonly class OutlinedTextRun
{
    /**
     * @param list<OutlinedGlyph> $glyphs
     */
    public function __construct(
        public array $glyphs,
        public float $scale,
        public float $letterSpacing = 0.0,
        public float $wordSpacing = 0.0,
    ) {
    }

    public function pathData(): string
    {
        return implode(' ', array_filter(array_map(
            static fn (OutlinedGlyph $glyph): string => $glyph->pathData,
            $this->glyphs,
        ), static fn (string $pathData): bool => '' !== $pathData));
    }

    public function advanceWidth(): float
    {
        $advanceWidth = 0.0;
        $glyphCount = \count($this->glyphs);

        foreach ($this->glyphs as $index => $glyph) {
            $advanceWidth += $glyph->positionedGlyph->xAdvance * $this->scale;

            if ($index < $glyphCount - 1) {
                $advanceWidth += $this->letterSpacing;

                if (32 === $glyph->positionedGlyph->codepoint) {
                    $advanceWidth += $this->wordSpacing;
                }
            }
        }

        return $advanceWidth;
    }

    public function toTextPath(): TextPath
    {
        return new TextPath($this->pathData(), $this->advanceWidth());
    }
}
