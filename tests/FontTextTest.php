<?php

declare(strict_types=1);

namespace Atelier\Text\Tests;

use Alto\Font\Font;
use Atelier\Text\Exception\InvalidArgumentException;
use Atelier\Text\FontText;
use Atelier\Text\Internal\TextArguments;
use Atelier\Text\Tests\Fixtures\TinyTrueTypeFont;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FontText::class)]
#[CoversClass(TextArguments::class)]
#[CoversClass(InvalidArgumentException::class)]
final class FontTextTest extends TestCase
{
    public function testItStoresTextOptionsAndDelegatesOutlineToFont(): void
    {
        $font = Font::fromFile(self::fontPath());
        $text = new FontText($font, 'A', 100.0, 5.0, 200.0, 1.5, 2.5);

        self::assertSame($font, $text->font());
        self::assertSame('A', $text->text);
        self::assertSame(100.0, $text->size);
        self::assertSame(5.0, $text->x);
        self::assertSame(200.0, $text->baselineY);
        self::assertSame(1.5, $text->letterSpacing);
        self::assertSame(2.5, $text->wordSpacing);
        self::assertSame('M 15 200 L 35 130 L 55 200 L 15 200 Z', $text->outline()->d());
    }

    public function testItRejectsNonPositiveOrNonFiniteSizes(): void
    {
        $font = Font::fromFile(self::fontPath());

        foreach ([0.0, -1.0, \INF, \NAN] as $size) {
            try {
                new FontText($font, 'A', $size);
                self::fail('Expected an invalid text size to be rejected.');
            } catch (InvalidArgumentException $exception) {
                self::assertSame('Text size must be finite and greater than zero.', $exception->getMessage());
            }
        }
    }

    public function testItRejectsNonFiniteCoordinatesAndSpacing(): void
    {
        $font = Font::fromFile(self::fontPath());
        $invalidArguments = [
            'Text x coordinate must be finite.' => static fn (): FontText => new FontText($font, 'A', 100.0, x: \NAN),
            'Text baseline must be finite.' => static fn (): FontText => new FontText($font, 'A', 100.0, baselineY: \INF),
            'Letter spacing must be finite.' => static fn (): FontText => new FontText($font, 'A', 100.0, letterSpacing: \NAN),
            'Word spacing must be finite.' => static fn (): FontText => new FontText($font, 'A', 100.0, wordSpacing: -\INF),
        ];

        foreach ($invalidArguments as $message => $createText) {
            try {
                $createText();
                self::fail("Expected {$message}");
            } catch (InvalidArgumentException $exception) {
                self::assertSame($message, $exception->getMessage());
            }
        }
    }

    private static function fontPath(): string
    {
        $path = sys_get_temp_dir().'/atelier-text-font-text-'.bin2hex(random_bytes(4)).'.ttf';
        TinyTrueTypeFont::write($path);

        return $path;
    }
}
