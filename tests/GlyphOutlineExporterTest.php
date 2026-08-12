<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Exception\InvalidFontException;
use Alto\Font\Glyph\Contour;
use Alto\Font\Glyph\GlyphId;
use Alto\Font\Glyph\GlyphOutline;
use Alto\Font\Glyph\PathCommand;
use Atelier\Text\GlyphOutlineExporter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GlyphOutlineExporter::class)]
final class GlyphOutlineExporterTest extends TestCase
{
    public function testItExportsScaledSvgPathData(): void
    {
        $outline = new GlyphOutline(new GlyphId(1), [
            new Contour([
                PathCommand::moveTo(100.0, 0.0),
                PathCommand::lineTo(300.0, 700.0),
                PathCommand::closePath(),
            ]),
        ]);

        self::assertSame('M 15 200 L 35 130 Z', GlyphOutlineExporter::toPathData(
            $outline,
            unitsPerEm: 1000,
            fontSize: 100.0,
            x: 5.0,
            baselineY: 200.0,
        ));
    }

    public function testItExportsEmptyOutlinesAsEmptyPathData(): void
    {
        self::assertSame('', GlyphOutlineExporter::toPathData(new GlyphOutline(new GlyphId(0), []), 1000));
    }

    public function testItRejectsInvalidUnitsPerEm(): void
    {
        $this->expectException(InvalidFontException::class);
        $this->expectExceptionMessage('Units per em must be positive');

        GlyphOutlineExporter::toPathData(new GlyphOutline(new GlyphId(0), []), 0);
    }

    public function testItExportsQuadraticCurvesAndFormatsExactZeroAsPlainZero(): void
    {
        $outline = new GlyphOutline(new GlyphId(2), [
            new Contour([
                PathCommand::moveTo(0.0, 0.0),
                PathCommand::quadraticTo(50.0, 100.0, 100.0, 0.0),
                PathCommand::closePath(),
            ]),
        ]);

        self::assertSame('M 0 0 Q 5 -10 10 0 Z', GlyphOutlineExporter::toPathData(
            $outline,
            unitsPerEm: 1000,
            fontSize: 100.0,
        ));
    }
}
