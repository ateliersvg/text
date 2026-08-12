<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Svg;

use Alto\Font\Font;
use Atelier\Text\Svg\SvgTextBox;
use Atelier\Text\Svg\SvgTextDocumentRenderer;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use Atelier\Text\TextToOutlines;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgTextDocumentRenderer::class)]
final class SvgTextDocumentRendererTest extends TestCase
{
    public function testItRendersOutlinedRunsAsGroupedSvgPaths(): void
    {
        $font = Font::fromFile(self::fontPath());
        $run = TextToOutlines::fromFont($font)->convertRun('A', 100.0, x: 15.0, baselineY: 95.0);
        $svg = new SvgTextDocumentRenderer()->render($run, SvgTextBox::fromFont($font, 100.0, $run->advanceWidth()));
        $markup = $svg->toPrettyString();

        self::assertStringContainsString('<svg', $markup);
        self::assertStringNotContainsString(' id="', $markup);
        self::assertStringContainsString('data-char="A"', $markup);
        self::assertStringContainsString('<path d="M 25 95 L 45 25 L 65 95 L 25 95 Z"', $markup);
    }

    public function testItAddsIdsOnlyWithAnExplicitPrefix(): void
    {
        $font = Font::fromFile(self::fontPath());
        $run = TextToOutlines::fromFont($font)->convertRun('A', 100.0, x: 15.0, baselineY: 95.0);
        $svg = new SvgTextDocumentRenderer()->render(
            $run,
            SvgTextBox::fromFont($font, 100.0, $run->advanceWidth()),
            'sample',
        );
        $markup = $svg->toPrettyString();

        self::assertStringContainsString('id="sample-text"', $markup);
        self::assertStringContainsString('id="sample-glyph-0"', $markup);
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-svg-document-renderer-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
