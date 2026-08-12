<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Font;
use Alto\Font\FontFinder;
use Alto\Font\FontQuery;
use Atelier\Text\Exception\InvalidArgumentException;
use Atelier\Text\Exception\MissingGlyphException;
use Atelier\Text\Internal\TextArguments;
use Atelier\Text\SvgText;
use Atelier\Text\SvgTextGenerator;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgText::class)]
#[CoversClass(SvgTextGenerator::class)]
#[CoversClass(TextArguments::class)]
#[CoversClass(InvalidArgumentException::class)]
final class SvgTextTest extends TestCase
{
    public function testItGeneratesPathDataFromAFontFile(): void
    {
        $path = SvgText::fromFile(self::fontPath())->path('A', 100.0);

        self::assertSame('M 10 0 L 30 -70 L 50 0 L 10 0 Z', $path->d());
        self::assertSame(60.0, $path->advanceWidth);
    }

    public function testItGeneratesSvgDocumentsFromAFontObject(): void
    {
        $font = Font::fromFile(self::fontPath());
        $svgText = SvgText::fromFont($font);
        $svg = $svgText->svg('AV', 100.0);
        $markup = $svg->toPrettyString();

        self::assertSame($font, $svgText->font());
        self::assertStringNotContainsString(' id="', $markup);
        self::assertSame(2, substr_count($markup, '<path'));
    }

    public function testItAddsCollisionSafeIdsOnlyWhenAPrefixIsProvided(): void
    {
        $svgText = SvgText::fromFile(self::fontPath());
        $headline = $svgText->svg('AV', 100.0, idPrefix: 'headline')->toPrettyString();
        $caption = $svgText->svg('AV', 100.0, idPrefix: 'caption')->toPrettyString();
        $markup = $headline.$caption;

        self::assertStringContainsString('id="headline-text"', $headline);
        self::assertStringContainsString('id="headline-glyph-0"', $headline);
        self::assertStringContainsString('id="caption-text"', $caption);
        self::assertMatchesRegularExpression('/id="[^"]+"/', $markup);
        preg_match_all('/id="([^"]+)"/', $markup, $matches);
        self::assertSame($matches[1], array_values(array_unique($matches[1])));
    }

    public function testItRejectsInvalidSvgIdPrefixes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SVG ID prefix must start with a letter');

        SvgText::fromFile(self::fontPath())->svg('A', 100.0, idPrefix: '123-invalid');
    }

    public function testItReportsMissingGlyphsThroughThePublicFacade(): void
    {
        $this->expectException(MissingGlyphException::class);
        $this->expectExceptionMessage('Font has no glyph for codepoint U+0042.');

        SvgText::fromFile(self::fontPath())->path('B', 100.0);
    }

    public function testItRejectsInvalidSizesThroughBothPublicRenderingMethods(): void
    {
        $svgText = SvgText::fromFile(self::fontPath());

        foreach ([0.0, -1.0, \INF, \NAN] as $size) {
            foreach (['path', 'svg'] as $method) {
                try {
                    $svgText->{$method}('A', $size);
                    self::fail("Expected {$method}() to reject an invalid size.");
                } catch (InvalidArgumentException $exception) {
                    self::assertSame('Text size must be finite and greater than zero.', $exception->getMessage());
                }
            }
        }
    }

    public function testItRejectsNonFiniteSvgSpacing(): void
    {
        $svgText = SvgText::fromFile(self::fontPath());

        foreach ([
            'Letter spacing must be finite.' => ['letterSpacing' => \NAN],
            'Word spacing must be finite.' => ['wordSpacing' => \INF],
        ] as $message => $arguments) {
            try {
                $svgText->svg(
                    'A',
                    100.0,
                    letterSpacing: $arguments['letterSpacing'] ?? 0.0,
                    wordSpacing: $arguments['wordSpacing'] ?? 0.0,
                );
                self::fail("Expected {$message}");
            } catch (InvalidArgumentException $exception) {
                self::assertSame($message, $exception->getMessage());
            }
        }
    }

    public function testItRendersEmptyTextPredictably(): void
    {
        $svgText = SvgText::fromFile(self::fontPath());

        self::assertTrue($svgText->path('', 100.0)->isEmpty());
        self::assertStringNotContainsString('class="glyph"', $svgText->svg('', 100.0)->toString());
    }

    public function testItResolvesFontsThroughFontFinder(): void
    {
        $directory = self::temporaryDirectory('svg-text-finder');
        TinyTrueTypeFont::write($directory.'/atelier-tiny.ttf');

        $path = SvgText::fromFinder(
            FontFinder::fromDirectories($directory),
            FontQuery::family('Atelier Tiny')->weight(400),
        )->path('AV', 72.0, baselineY: 84.0);

        self::assertFalse($path->isEmpty());
        self::assertStringStartsWith('M ', $path->d());
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-svg-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }

    private static function temporaryDirectory(string $name): string
    {
        $directory = sys_get_temp_dir().'/atelier-'.$name.'-'.bin2hex(random_bytes(4));

        if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
            self::fail(\sprintf('Could not create "%s".', $directory));
        }

        return $directory;
    }
}
