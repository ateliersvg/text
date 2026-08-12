<?php

declare(strict_types=1);

namespace Atelier\Text\Shaping;

use Alto\Font\Font;
use Alto\Font\Text\UnicodeString;
use Atelier\Text\Exception\MissingGlyphException;

/**
 * @internal direct cmap mapper used when no external shaping engine is configured
 */
final readonly class DirectTextShaper implements TextShaperInterface
{
    public function __construct(private Font $font)
    {
    }

    public function shape(string $text): GlyphRun
    {
        $glyphs = [];

        foreach (UnicodeString::codepoints($text) as $cluster => $codepoint) {
            $glyphId = $this->font->glyphIdForCodepoint($codepoint);

            if (null === $glyphId) {
                throw new MissingGlyphException($codepoint);
            }

            $metrics = $this->font->glyphMetrics($glyphId);
            $glyphs[] = new PositionedGlyph(
                glyphId: $glyphId,
                metrics: $metrics,
                cluster: $cluster,
                xAdvance: (float) $metrics->advanceWidth,
                codepoint: $codepoint,
            );
        }

        return new GlyphRun($glyphs);
    }
}
