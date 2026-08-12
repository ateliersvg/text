<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Outline;

use Atelier\Text\Outline\TextPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextPath::class)]
final class TextPathTest extends TestCase
{
    public function testItExposesPathDataAndAdvanceWidth(): void
    {
        $path = new TextPath('M 0 0 Z', 42.5);

        self::assertSame('M 0 0 Z', $path->d());
        self::assertSame(42.5, $path->advanceWidth);
        self::assertFalse($path->isEmpty());
    }

    public function testItReportsEmptyPathData(): void
    {
        self::assertTrue(new TextPath('', 0.0)->isEmpty());
    }
}
