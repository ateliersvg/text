<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Outline;

use Alto\Font\Font;
use Atelier\Text\Outline\OutlinedTextRun;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use Atelier\Text\TextToOutlines;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OutlinedTextRun::class)]
final class OutlinedTextRunTest extends TestCase
{
    public function testItExposesCombinedPathDataAndAdvanceWidth(): void
    {
        $run = TextToOutlines::fromFont(Font::fromFile(self::fontPath()))->convertRun(
            'A V',
            fontSize: 100.0,
            letterSpacing: 5.0,
            wordSpacing: 10.0,
        );

        self::assertSame(
            'M 10 0 L 30 -70 L 50 0 L 10 0 Z M 140 -70 L 160 0 L 180 -70 L 140 -70 Z',
            $run->pathData(),
        );
        self::assertSame(191.0, $run->advanceWidth());
        self::assertSame($run->pathData(), $run->toTextPath()->d());
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-outlined-text-run-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
