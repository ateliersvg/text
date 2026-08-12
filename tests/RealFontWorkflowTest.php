<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Atelier\Text\SvgText;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgText::class)]
final class RealFontWorkflowTest extends TestCase
{
    public function testItGeneratesSvgPathDataFromARealTransformedWoff2Font(): void
    {
        $fontPath = __DIR__.'/Fixtures/Fonts/Inter-Regular-latin.woff2';
        $svgText = SvgText::fromFile($fontPath);
        $path = $svgText->path('Abc deF 01 23 //', 72.0, baselineY: 84.0);

        self::assertSame('Inter', $svgText->font()->getDescriptor()->family);
        self::assertFalse($path->isEmpty());
        self::assertStringStartsWith('M ', $path->d());
        self::assertStringNotContainsString('<text', $path->d());
    }
}
