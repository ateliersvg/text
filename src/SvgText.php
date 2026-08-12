<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Descriptor\FontStyle;
use Alto\Font\Font;
use Alto\Font\FontFinder;
use Alto\Font\FontQuery;
use Atelier\Svg\Svg;
use Atelier\Text\Outline\TextPath;

/**
 * Primary consumer facade: font-backed text to SVG path data or SVG document.
 */
final readonly class SvgText
{
    public function __construct(private Font $font)
    {
    }

    public static function fromFile(string|\Stringable $file, int $faceIndex = 0): self
    {
        return new self(Font::fromFile($file, $faceIndex));
    }

    public static function fromFont(Font $font): self
    {
        return new self($font);
    }

    public static function fromFinder(
        FontFinder $finder,
        string|FontQuery $query,
        ?int $weight = null,
        ?FontStyle $style = null,
    ): self {
        return new self($finder->get($query, $weight, $style));
    }

    public function font(): Font
    {
        return $this->font;
    }

    public function path(
        string $text,
        float $size,
        float $x = 0.0,
        float $baselineY = 0.0,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
    ): TextPath {
        return TextPathGenerator::fromFont($this->font)->generate(
            $text,
            $size,
            $x,
            $baselineY,
            $letterSpacing,
            $wordSpacing,
        );
    }

    public function svg(
        string $text,
        float $size,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
        ?string $idPrefix = null,
    ): Svg {
        return new SvgTextGenerator()->generate($this->font, $text, $size, $letterSpacing, $wordSpacing, $idPrefix);
    }
}
