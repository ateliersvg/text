<h1 align="center">Atelier Text</h1>

<p align="center">Turn Unicode text into positioned glyphs, vector outlines, and SVG, in PHP.</p>

<p align="center">
  <img alt="PHP Version" src="https://img.shields.io/badge/PHP-8.4%2B-e8657f?labelColor=14141c">
  <img alt="PHPUnit" src="https://img.shields.io/badge/PHPUnit-13-e8657f?labelColor=14141c">
  <img alt="PHPStan" src="https://img.shields.io/badge/PHPStan-max-e8657f?labelColor=14141c">
  <img alt="License" src="https://img.shields.io/badge/License-MIT-e8657f?labelColor=14141c">
</p>

Text drawn as paths owes nothing to the fonts installed on the machine that opens it. This
package reads a font file, looks each codepoint up in its `cmap`, positions the glyphs, and
returns their outlines as SVG path data or a complete document.

```php
echo SvgText::fromFile('Inter-Regular.woff2')->path('Hello', size: 72)->d();
```

Font parsing and discovery come from `alto/font`; document construction comes from
`atelier/svg`. Backed by an extensive test suite and PHPStan at its highest level.

**[Path data](#path-data) · [Whole documents](#whole-documents) · [Choosing a font](#choosing-a-font) ·
[What it does not do](#what-it-does-not-do) · [Public API](#public-api) ·
[Documentation](#documentation)**

## Installation

```bash
composer require atelier/text
```

Requires PHP 8.4 and `ext-mbstring`. WOFF2 files also need a Brotli decoder that `alto/font`
supports: either `ext-brotli` or the `brotli` command-line binary. Checking which one you have,
and what happens without either, is in [Installation](docs/installation.md).

## Quick start

```php
use Atelier\Text\SvgText;

$path = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2')
    ->path('Hello', size: 72, baselineY: 84);

echo '<svg viewBox="0 0 240 100" xmlns="http://www.w3.org/2000/svg">';
echo '<path d="'.htmlspecialchars($path->d(), \ENT_QUOTES).'" />';
echo '</svg>';
```

Both calls, side by side, are in [Getting started](docs/getting-started.md).

## Path data

`path()` returns a `TextPath`, whose `d()` is the combined outline of the whole run. That is the
form to use when the text has to sit inside a drawing you are already building, or be handed to
something that only speaks path data.

Letter spacing and word spacing are arguments, not post-processing. `baselineY` is the line the
glyphs sit on rather than the top of the text, which is the one that catches people out. See
[Path data](docs/outlines.md).

## Whole documents

`svg()` returns an `atelier/svg` document instead, with one group per glyph carrying a
`data-char` attribute.

```php
$svg = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2')
    ->svg('Hello', size: 72, idPrefix: 'headline');

echo $svg->toPrettyString();
```

Groups carry no `id` by default. Passing `idPrefix` makes them deterministic, which is what an
animation or a stylesheet needs to address a single glyph. See
[SVG documents](docs/documents.md).

## Choosing a font

A path is one option; a query against a directory of fonts is the other.

```php
use Alto\Font\FontFinder;
use Alto\Font\FontQuery;
use Atelier\Text\SvgText;

$finder = FontFinder::fromDirectories(__DIR__.'/fonts');

$font = SvgText::fromFinder($finder, FontQuery::family('Inter')->weight(700));
$path = $font->path('Hello', size: 72);
```

Every format the installed `alto/font` supports is available here. A query that matches nothing,
and how a finder caches what it scans, are in [Loading a font](docs/fonts.md).

## What it does not do

The mapper performs one direct `cmap` lookup per codepoint and returns a left-to-right run. It
does not do font fallback, bidirectional text, ligatures, kerning, script shaping, or anything
HarfBuzz-compatible.

That is a deliberate boundary rather than a gap: `Shaping\TextShaperInterface` is the seam where
a real shaping engine plugs in, and everything downstream consumes its `GlyphRun`. What that
contract already allows, and what writing a replacement takes, are in
[Shaping](docs/shaping.md).

A codepoint the font has no glyph for throws `MissingGlyphException`. A size that is not finite
and positive, or a non-finite coordinate, throws `InvalidArgumentException`. Both, and what
`alto/font` throws instead when a file will not load, are in [Errors](docs/errors.md).

## Public API

| Type | What it is for |
|---|---|
| `SvgText` | the facade: path data and documents, from a file or a finder |
| `FontText` | an immutable font-backed text specification |
| `TextPathGenerator` | one combined `Outline\TextPath` |
| `Outline\OutlinerInterface` | a seam for custom outline generation |
| `Shaping\TextShaperInterface` | the shaped-run contract, with `GlyphRun`, `PositionedGlyph`, `TextDirection` |

Converters and renderers marked `@internal` are outside the compatibility promise. Most
applications need `SvgText` and nothing else.

## Example

```bash
php examples/woff2-demo.php
```

Writes `examples/output/woff2/index.html` from the bundled open-source Inter fixture.

## Documentation

- [Installation](docs/installation.md): the requirements, and checking a Brotli decoder.
- [Getting started](docs/getting-started.md): the two calls, and which one to reach for.
- [Loading a font](docs/fonts.md): a path, a finder, and a query that matches nothing.
- [Path data](docs/outlines.md): placement, spacing, and what a `TextPath` carries.
- [SVG documents](docs/documents.md): glyph groups, deterministic ids, and composing.
- [Shaping](docs/shaping.md): what the mapper does, what it will not, and the seam.
- [Errors](docs/errors.md): every exception, and which layer throws it.

The full documentation is published at
[ateliersvg.com/text](https://ateliersvg.com/text/).

## Contributing

Before submitting code, run:

```bash
composer qa   # PHP-CS-Fixer, PHPStan at level max, and PHPUnit
```

Changes to public behaviour need a test and a documentation update.

## Support

Bug reports, security disclosures, and contribution guidelines are collected at
[ateliersvg.com/support](https://ateliersvg.com/support/).

Atelier is maintained by Simon André.

## License

Atelier Text is released under the [MIT License](LICENSE).
