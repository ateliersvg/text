<?php

declare(strict_types=1);

namespace Atelier\Text\Exception;

final class MissingGlyphException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(public readonly int $codepoint)
    {
        parent::__construct(\sprintf('Font has no glyph for codepoint U+%04X.', $codepoint));
    }
}
