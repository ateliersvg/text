<?php

declare(strict_types=1);

namespace Atelier\Text\Shaping;

enum TextDirection: string
{
    case LeftToRight = 'ltr';
    case RightToLeft = 'rtl';
}
