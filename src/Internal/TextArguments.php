<?php

declare(strict_types=1);

namespace Atelier\Text\Internal;

use Atelier\Text\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class TextArguments
{
    public static function validate(
        float $size,
        float $x = 0.0,
        float $baselineY = 0.0,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
    ): void {
        if (!is_finite($size) || $size <= 0.0) {
            throw new InvalidArgumentException('Text size must be finite and greater than zero.');
        }

        self::finite($x, 'Text x coordinate');
        self::finite($baselineY, 'Text baseline');
        self::finite($letterSpacing, 'Letter spacing');
        self::finite($wordSpacing, 'Word spacing');
    }

    public static function validateIdPrefix(?string $idPrefix): void
    {
        if (null !== $idPrefix && 1 !== preg_match('/\A[A-Za-z][A-Za-z0-9_-]*\z/', $idPrefix)) {
            throw new InvalidArgumentException('SVG ID prefix must start with a letter and contain only letters, digits, underscores, or hyphens.');
        }
    }

    private static function finite(float $value, string $name): void
    {
        if (!is_finite($value)) {
            throw new InvalidArgumentException("{$name} must be finite.");
        }
    }
}
