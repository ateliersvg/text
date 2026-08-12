<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Svg;

use Alto\Font\Font;
use Atelier\Text\Svg\SvgTextBox;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgTextBox::class)]
final class SvgTextBoxTest extends TestCase
{
    public function testItComputesViewportMetricsFromFontMetrics(): void
    {
        $box = SvgTextBox::fromFont(Font::fromFile(self::fontPath()), 100.0, advanceWidth: 120.0);

        self::assertSame(150.0, $box->width);
        self::assertSame(130.0, $box->height);
        self::assertSame(15.0, $box->padding);
        self::assertSame(95.0, $box->baselineY);
        self::assertSame(230.0, $box->withAdvanceWidth(200.0)->width);
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-svg-text-box-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
