<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Glyph\GlyphId;
use Alto\Font\Glyph\GlyphMetrics;
use Alto\Font\Glyph\GlyphOutline;
use Atelier\Text\OutlinedGlyph;
use Atelier\Text\Shaping\PositionedGlyph;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OutlinedGlyph::class)]
final class OutlinedGlyphTest extends TestCase
{
    public function testItStoresPositionedOutlineData(): void
    {
        $glyphId = new GlyphId(1);
        $positioned = new PositionedGlyph($glyphId, new GlyphMetrics($glyphId, 600, 0), 0, 600.0);
        $outline = new GlyphOutline($glyphId, []);
        $outlined = new OutlinedGlyph($positioned, $outline, 'M 0 0 Z', 10.0, 20.0);

        self::assertSame($positioned, $outlined->positionedGlyph);
        self::assertSame($outline, $outlined->outline);
        self::assertSame('M 0 0 Z', $outlined->pathData);
        self::assertSame(10.0, $outlined->x);
        self::assertSame(20.0, $outlined->baselineY);
    }
}
