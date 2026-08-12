<?php

declare(strict_types=1);

namespace Atelier\Text\Outline;

use Atelier\Text\FontText;
use Atelier\Text\TextToOutlines;

/**
 * @internal default outliner used by FontText and TextPathGenerator
 */
final readonly class SimpleOutliner implements OutlinerInterface
{
    public function outline(FontText $text): TextPath
    {
        $font = $text->font();
        $run = TextToOutlines::fromFont($font)->convertRun(
            $text->text,
            $text->size,
            $text->x,
            $text->baselineY,
            $text->letterSpacing,
            $text->wordSpacing,
        );

        return $run->toTextPath();
    }
}
