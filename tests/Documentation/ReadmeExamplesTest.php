<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Documentation;

use Alto\Font\FontFinder;
use Alto\Font\FontQuery;
use Atelier\Text\SvgText;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class ReadmeExamplesTest extends TestCase
{
    private const string FONT = __DIR__.'/../Fixtures/Fonts/Inter-Regular-latin.woff2';

    public function testPathDataExampleRuns(): void
    {
        $path = SvgText::fromFile(self::FONT)->path('Hello', size: 72, baselineY: 84);

        self::assertFalse($path->isEmpty());
        self::assertStringStartsWith('M ', $path->d());
    }

    public function testSvgDocumentExampleRuns(): void
    {
        $svg = SvgText::fromFile(self::FONT)->svg('Hello', size: 72, idPrefix: 'headline');

        self::assertStringContainsString('id="headline-text"', $svg->toPrettyString());
    }

    public function testFontFinderExampleRuns(): void
    {
        $font = SvgText::fromFinder(
            FontFinder::fromDirectories(\dirname(self::FONT)),
            FontQuery::family('Inter')->weight(400),
        );

        self::assertFalse($font->path('Hello', size: 72)->isEmpty());
    }
}
