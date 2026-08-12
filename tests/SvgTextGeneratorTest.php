<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Font;
use Atelier\Text\SvgTextGenerator;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgTextGenerator::class)]
final class SvgTextGeneratorTest extends TestCase
{
    public function testItGeneratesOneGroupPerLetterEachWithASinglePath(): void
    {
        $font = Font::fromFile(self::fontPath());

        $svg = new SvgTextGenerator()->generate($font, 'AV', 100.0);
        $markup = $svg->toPrettyString();

        self::assertStringNotContainsString(' id="', $markup);
        self::assertSame(2, substr_count($markup, 'class="glyph"'));
        self::assertStringContainsString('data-char="A"', $markup);
        self::assertStringContainsString('data-char="V"', $markup);
        self::assertSame(2, substr_count($markup, '<path'));
    }

    public function testItAddsPrefixedIdsAndTheRightCharacter(): void
    {
        $font = Font::fromFile(self::fontPath());

        $svg = new SvgTextGenerator()->generate($font, 'AV', 100.0, idPrefix: 'sample');
        $markup = $svg->toPrettyString();

        self::assertStringContainsString('id="sample-glyph-0"', $markup);
        self::assertStringContainsString('id="sample-glyph-1"', $markup);
        self::assertStringContainsString('data-char="A"', $markup);
        self::assertStringContainsString('data-char="V"', $markup);
    }

    public function testItSizesTheViewBoxToFitTheText(): void
    {
        $font = Font::fromFile(self::fontPath());

        $short = new SvgTextGenerator()->generate($font, 'A', 100.0);
        $long = new SvgTextGenerator()->generate($font, 'AAAA', 100.0);

        self::assertLessThan(self::width($long), self::width($short));
    }

    public function testItProducesEmptyGlyphGroupsForUnsupportedCharactersLikeSpace(): void
    {
        $font = Font::fromFile(self::fontPath());

        $svg = new SvgTextGenerator()->generate($font, 'A A', 100.0);
        $markup = $svg->toPrettyString();

        self::assertSame(3, substr_count($markup, 'class="glyph"'));
        self::assertStringContainsString('data-char=" "', $markup);
    }

    private static function width(\Atelier\Svg\Svg $svg): float
    {
        self::assertMatchesRegularExpression('/width="([\d.]+)"/', $svg->toString());
        preg_match('/width="([\d.]+)"/', $svg->toString(), $matches);

        return (float) $matches[1];
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-svg-generator-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
