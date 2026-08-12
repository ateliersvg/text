<?php

declare(strict_types=1);

namespace Atelier\Text\Shaping;

interface TextShaperInterface
{
    public function shape(string $text): GlyphRun;
}
