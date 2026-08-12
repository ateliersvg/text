<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Outline;

use Alto\Font\Font;
use Atelier\Text\FontText;
use Atelier\Text\Outline\SimpleOutliner;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimpleOutliner::class)]
final class SimpleOutlinerTest extends TestCase
{
    public function testItOutlinesTextIntoOnePath(): void
    {
        $font = Font::fromFile(self::fontPath());
        $path = new SimpleOutliner()->outline(new FontText($font, 'AV', 100.0, 5.0, 200.0, 5.0));

        self::assertSame(
            'M 15 200 L 35 130 L 55 200 L 15 200 Z M 80 130 L 100 200 L 120 130 L 80 130 Z',
            $path->d(),
        );
        self::assertSame(126.0, $path->advanceWidth);
    }

    public function testItAppliesWordSpacingAfterSpaces(): void
    {
        $font = Font::fromFile(self::fontPath());
        $path = new SimpleOutliner()->outline(new FontText($font, 'A V', 100.0, 0.0, 0.0, 5.0, 10.0));

        self::assertSame(
            'M 10 0 L 30 -70 L 50 0 L 10 0 Z M 140 -70 L 160 0 L 180 -70 L 140 -70 Z',
            $path->d(),
        );
        self::assertSame(191.0, $path->advanceWidth);
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-simple-outliner-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
