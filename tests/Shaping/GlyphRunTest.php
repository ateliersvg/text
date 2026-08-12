<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Shaping;

use Alto\Font\Glyph\GlyphId;
use Alto\Font\Glyph\GlyphMetrics;
use Atelier\Text\Shaping\GlyphRun;
use Atelier\Text\Shaping\PositionedGlyph;
use Atelier\Text\Shaping\TextDirection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GlyphRun::class)]
final class GlyphRunTest extends TestCase
{
    public function testItStoresGlyphsAndDirection(): void
    {
        $glyphId = new GlyphId(1);
        $glyph = new PositionedGlyph($glyphId, new GlyphMetrics($glyphId, 600, 0), 0, 600.0);
        $run = new GlyphRun([$glyph], TextDirection::RightToLeft);

        self::assertSame([$glyph], $run->glyphs);
        self::assertSame(TextDirection::RightToLeft, $run->direction);
    }
}
