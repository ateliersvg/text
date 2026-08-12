<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Shaping;

use Alto\Font\Font;
use Atelier\Text\Exception\MissingGlyphException;
use Atelier\Text\Shaping\DirectTextShaper;
use Atelier\Text\Shaping\TextDirection;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DirectTextShaper::class)]
#[CoversClass(MissingGlyphException::class)]
final class DirectTextShaperTest extends TestCase
{
    public function testItMapsTextByDirectCmapLookup(): void
    {
        $run = new DirectTextShaper(Font::fromFile(self::fontPath()))->shape('AV');

        self::assertSame(TextDirection::LeftToRight, $run->direction);
        self::assertSame([1, 2], array_map(
            static fn ($glyph): int => $glyph->glyphId->value,
            $run->glyphs,
        ));
        self::assertSame([0, 1], array_map(
            static fn ($glyph): int => $glyph->cluster,
            $run->glyphs,
        ));
        self::assertSame([65, 86], array_map(
            static fn ($glyph): ?int => $glyph->codepoint,
            $run->glyphs,
        ));
    }

    public function testItRejectsMissingGlyphs(): void
    {
        $this->expectException(MissingGlyphException::class);
        $this->expectExceptionMessage('Font has no glyph for codepoint U+0042.');

        try {
            new DirectTextShaper(Font::fromFile(self::fontPath()))->shape('B');
        } catch (MissingGlyphException $exception) {
            self::assertSame(0x42, $exception->codepoint);

            throw $exception;
        }
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-direct-shaper-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
