<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Font;
use Atelier\Text\Outline\OutlinedTextRun;
use Atelier\Text\Shaping\DirectTextShaper;
use Atelier\Text\Shaping\TextShaperInterface;

/**
 * @internal prefer SvgText::path() or Outline\OutlinerInterface for consumer code
 */
final readonly class TextToOutlines
{
    public function __construct(
        private Font $font,
        private TextShaperInterface $shaper,
    ) {
    }

    public static function fromFont(Font $font): self
    {
        return new self($font, new DirectTextShaper($font));
    }

    public function convertRun(
        string $text,
        float $fontSize,
        float $x = 0.0,
        float $baselineY = 0.0,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
    ): OutlinedTextRun {
        $unitsPerEm = $this->font->face()->unitsPerEm;

        return new OutlinedTextRun(
            glyphs: $this->convert($text, $fontSize, $x, $baselineY, $letterSpacing, $wordSpacing),
            scale: $fontSize / $unitsPerEm,
            letterSpacing: $letterSpacing,
            wordSpacing: $wordSpacing,
        );
    }

    /**
     * @return list<OutlinedGlyph>
     */
    public function convert(
        string $text,
        float $fontSize,
        float $x = 0.0,
        float $baselineY = 0.0,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
    ): array {
        $run = $this->shaper->shape($text);
        $unitsPerEm = $this->font->face()->unitsPerEm;
        $scale = $fontSize / $unitsPerEm;
        $cursorX = $x;
        $cursorY = $baselineY;
        $outlined = [];
        $glyphCount = \count($run->glyphs);

        foreach ($run->glyphs as $index => $positionedGlyph) {
            $glyphX = $cursorX + $positionedGlyph->xOffset * $scale;
            $glyphBaselineY = $cursorY - $positionedGlyph->yOffset * $scale;
            $outline = $this->font->glyphOutline($positionedGlyph->glyphId);
            $pathData = GlyphOutlineExporter::toPathData($outline, $unitsPerEm, $fontSize, $glyphX, $glyphBaselineY);

            $outlined[] = new OutlinedGlyph(
                positionedGlyph: $positionedGlyph,
                outline: $outline,
                pathData: $pathData,
                x: $glyphX,
                baselineY: $glyphBaselineY,
            );

            $cursorX += $positionedGlyph->xAdvance * $scale;
            $cursorY += $positionedGlyph->yAdvance * $scale;

            if ($index < $glyphCount - 1) {
                $cursorX += $letterSpacing;

                if (32 === $positionedGlyph->codepoint) {
                    $cursorX += $wordSpacing;
                }
            }
        }

        return $outlined;
    }
}
