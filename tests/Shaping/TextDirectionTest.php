<?php

declare(strict_types=1);

namespace Atelier\Text\Tests\Shaping;

use Atelier\Text\Shaping\TextDirection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextDirection::class)]
final class TextDirectionTest extends TestCase
{
    public function testItExposesStableValues(): void
    {
        self::assertSame('ltr', TextDirection::LeftToRight->value);
        self::assertSame('rtl', TextDirection::RightToLeft->value);
    }
}
