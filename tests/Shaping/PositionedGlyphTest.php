<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Shaping;

use Alto\Font\Glyph\GlyphId;
use Alto\Font\Glyph\GlyphMetrics;
use Atelier\Text\Shaping\PositionedGlyph;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PositionedGlyph::class)]
final class PositionedGlyphTest extends TestCase
{
    public function testItStoresPositionedGlyphData(): void
    {
        $glyphId = new GlyphId(2);
        $metrics = new GlyphMetrics($glyphId, 610, 20);
        $glyph = new PositionedGlyph($glyphId, $metrics, 3, 610.0, 1.0, 2.0, 3.0, 86);

        self::assertSame($glyphId, $glyph->glyphId);
        self::assertSame($metrics, $glyph->metrics);
        self::assertSame(3, $glyph->cluster);
        self::assertSame(610.0, $glyph->xAdvance);
        self::assertSame(1.0, $glyph->yAdvance);
        self::assertSame(2.0, $glyph->xOffset);
        self::assertSame(3.0, $glyph->yOffset);
        self::assertSame(86, $glyph->codepoint);
    }
}
