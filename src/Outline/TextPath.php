<?php

declare(strict_types=1);

namespace Atelier\Text\Outline;

final readonly class TextPath
{
    public function __construct(
        private string $d,
        public float $advanceWidth,
    ) {
    }

    public function d(): string
    {
        return $this->d;
    }

    public function isEmpty(): bool
    {
        return '' === $this->d;
    }
}
