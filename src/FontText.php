<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Font;
use Atelier\Text\Internal\TextArguments;
use Atelier\Text\Outline\SimpleOutliner;
use Atelier\Text\Outline\TextPath;

final readonly class FontText
{
    public function __construct(
        private Font $font,
        public string $text,
        public float $size,
        public float $x = 0.0,
        public float $baselineY = 0.0,
        public float $letterSpacing = 0.0,
        public float $wordSpacing = 0.0,
    ) {
        TextArguments::validate($size, $x, $baselineY, $letterSpacing, $wordSpacing);
    }

    public function font(): Font
    {
        return $this->font;
    }

    public function outline(): TextPath
    {
        return new SimpleOutliner()->outline($this);
    }
}
