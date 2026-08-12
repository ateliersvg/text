<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Font;
use Atelier\Text\Outline\OutlinerInterface;
use Atelier\Text\Outline\SimpleOutliner;
use Atelier\Text\Outline\TextPath;

/**
 * Advanced injectable service.
 */
final readonly class TextPathGenerator
{
    public function __construct(
        private Font $font,
        private OutlinerInterface $outliner = new SimpleOutliner(),
    ) {
    }

    public static function fromFile(string|\Stringable $file): self
    {
        return new self(Font::fromFile($file));
    }

    public static function fromFont(Font $font): self
    {
        return new self($font);
    }

    public function generate(
        string $text,
        float $size,
        float $x = 0.0,
        float $baselineY = 0.0,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
    ): TextPath {
        return $this->outliner->outline(new FontText($this->font, $text, $size, $x, $baselineY, $letterSpacing, $wordSpacing));
    }
}
