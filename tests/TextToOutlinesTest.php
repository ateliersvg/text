<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Font;
use Alto\Font\Glyph\GlyphMetrics;
use Atelier\Text\Shaping\GlyphRun;
use Atelier\Text\Shaping\PositionedGlyph;
use Atelier\Text\Shaping\TextShaperInterface;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use Atelier\Text\TextToOutlines;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextToOutlines::class)]
final class TextToOutlinesTest extends TestCase
{
    public function testItConvertsTextToPositionedGlyphOutlines(): void
    {
        $outlines = TextToOutlines::fromFont(Font::fromFile(self::fontPath()))->convert(
            'AV',
            fontSize: 100.0,
            x: 5.0,
            baselineY: 200.0,
            letterSpacing: 5.0,
        );

        self::assertCount(2, $outlines);
        self::assertSame(5.0, $outlines[0]->x);
        self::assertSame(70.0, $outlines[1]->x);
        self::assertSame('M 15 200 L 35 130 L 55 200 L 15 200 Z', $outlines[0]->pathData);
    }

    public function testItConvertsTextToOutlinedRuns(): void
    {
        $run = TextToOutlines::fromFont(Font::fromFile(self::fontPath()))->convertRun(
            'AV',
            fontSize: 100.0,
            letterSpacing: 5.0,
        );

        self::assertSame(
            'M 10 0 L 30 -70 L 50 0 L 10 0 Z M 75 -70 L 95 0 L 115 -70 L 75 -70 Z',
            $run->pathData(),
        );
        self::assertSame(126.0, $run->advanceWidth());
    }

    public function testItAppliesShaperOffsets(): void
    {
        $font = Font::fromFile(self::fontPath());
        $glyphId = $font->glyphIdForCodepoint(65);
        self::assertNotNull($glyphId);

        $converter = new TextToOutlines($font, new readonly class($glyphId, $font->glyphMetrics($glyphId)) implements TextShaperInterface {
            public function __construct(
                private \Alto\Font\Glyph\GlyphId $glyphId,
                private GlyphMetrics $metrics,
            ) {
            }

            public function shape(string $text): GlyphRun
            {
                return new GlyphRun([
                    new PositionedGlyph(
                        glyphId: $this->glyphId,
                        metrics: $this->metrics,
                        cluster: 0,
                        xAdvance: 600.0,
                        yAdvance: 0.0,
                        xOffset: 20.0,
                        yOffset: 10.0,
                    ),
                ]);
            }
        });

        $outlined = $converter->convert('A', fontSize: 100.0, x: 5.0, baselineY: 200.0);

        self::assertSame(7.0, $outlined[0]->x);
        self::assertSame(199.0, $outlined[0]->baselineY);
        self::assertSame('M 17 199 L 37 129 L 57 199 L 17 199 Z', $outlined[0]->pathData);
    }

    public function testItAppliesWordSpacingAfterSpaces(): void
    {
        $outlines = TextToOutlines::fromFont(Font::fromFile(self::fontPath()))->convert(
            'A V',
            fontSize: 100.0,
            letterSpacing: 5.0,
            wordSpacing: 10.0,
        );

        self::assertSame(0.0, $outlines[0]->x);
        self::assertSame(65.0, $outlines[1]->x);
        self::assertSame(130.0, $outlines[2]->x);
        self::assertSame('', $outlines[1]->pathData);
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-text-to-outlines-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
