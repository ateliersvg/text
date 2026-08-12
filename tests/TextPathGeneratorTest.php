<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Font;
use Atelier\Text\FontText;
use Atelier\Text\Outline\OutlinerInterface;
use Atelier\Text\Outline\TextPath;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use Atelier\Text\TextPathGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextPathGenerator::class)]
final class TextPathGeneratorTest extends TestCase
{
    public function testItGeneratesPathsFromFonts(): void
    {
        $generator = TextPathGenerator::fromFont(Font::fromFile(self::fontPath()));

        self::assertSame('M 10 0 L 30 -70 L 50 0 L 10 0 Z', $generator->generate('A', 100.0)->d());
    }

    public function testItGeneratesPathsFromFiles(): void
    {
        self::assertSame('M 10 0 L 30 -70 L 50 0 L 10 0 Z', TextPathGenerator::fromFile(self::fontPath())
            ->generate('A', 100.0)
            ->d());
    }

    public function testItAcceptsSpacingOptions(): void
    {
        $path = TextPathGenerator::fromFile(self::fontPath())->generate(
            'A V',
            size: 100.0,
            letterSpacing: 5.0,
            wordSpacing: 10.0,
        );

        self::assertSame(
            'M 10 0 L 30 -70 L 50 0 L 10 0 Z M 140 -70 L 160 0 L 180 -70 L 140 -70 Z',
            $path->d(),
        );
        self::assertSame(191.0, $path->advanceWidth);
    }

    public function testItAcceptsInjectableOutliners(): void
    {
        $generator = new TextPathGenerator(Font::fromFile(self::fontPath()), new class implements OutlinerInterface {
            public function outline(FontText $text): TextPath
            {
                return new TextPath($text->text, $text->size);
            }
        });

        $path = $generator->generate('custom', 12.0);

        self::assertSame('custom', $path->d());
        self::assertSame(12.0, $path->advanceWidth);
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-text-path-generator-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
