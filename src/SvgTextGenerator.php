<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Font;
use Atelier\Svg\Svg;
use Atelier\Text\Internal\TextArguments;
use Atelier\Text\Svg\SvgTextBox;
use Atelier\Text\Svg\SvgTextDocumentRenderer;

/**
 * @internal use SvgText::svg() for consumer code
 */
final class SvgTextGenerator
{
    public function generate(
        Font $font,
        string $text,
        float $size,
        float $letterSpacing = 0.0,
        float $wordSpacing = 0.0,
        ?string $idPrefix = null,
    ): Svg {
        TextArguments::validate($size, letterSpacing: $letterSpacing, wordSpacing: $wordSpacing);
        TextArguments::validateIdPrefix($idPrefix);

        $box = SvgTextBox::fromFont($font, $size);

        $run = TextToOutlines::fromFont($font)->convertRun(
            $text,
            $size,
            $box->padding,
            $box->baselineY,
            $letterSpacing,
            $wordSpacing,
        );
        $box = $box->withAdvanceWidth($run->advanceWidth());

        return new SvgTextDocumentRenderer()->render($run, $box, $idPrefix);
    }
}
