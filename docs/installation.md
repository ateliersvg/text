---
order: 10
---
# Installation

```bash
composer require atelier/text
```

## Requirements

| Requirement | Why |
|---|---|
| PHP 8.4 or later | the minimum runtime supported by this package |
| `ext-mbstring` | splitting text into codepoints |
| `alto/font` | parsing font files and reading glyph outlines |
| `atelier/svg` | building the document `svg()` returns |

Composer installs the last two. The PHP version and `mbstring` are yours to
provide.

## WOFF2 needs a Brotli decoder

WOFF2 files are Brotli-compressed, and PHP has no decoder in its standard
build. `alto/font` accepts either of two:

```bash
pecl install brotli        # the extension
brew install brotli        # the command-line binary
```

The extension is faster because it decodes in process. The binary works
everywhere and needs no compilation, which is usually what a container has.

TrueType and OpenType files need neither: they are not compressed.

## Verify the installation

```php
use Atelier\Text\SvgText;

$path = SvgText::fromFile('Inter-Regular.woff2')->path('Hi', size: 48);

var_dump($path->isEmpty());   // false
echo substr($path->d(), 0, 40);
```

Path data printed means the font parsed, the codepoints resolved, and the
outlines came back. An empty path means the text produced no glyphs.

A `MissingGlyphException` at this point means the font has no glyph for one of
your codepoints, which is a font problem rather than an installation one. See
[Errors](errors.md).

Next: [Getting started](getting-started.md).
