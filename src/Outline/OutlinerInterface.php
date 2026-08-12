<?php

declare(strict_types=1);

namespace Atelier\Text\Outline;

use Atelier\Text\FontText;

interface OutlinerInterface
{
    public function outline(FontText $text): TextPath;
}
