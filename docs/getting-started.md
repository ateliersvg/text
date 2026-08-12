---
order: 20
---
# Getting Started

Two calls cover most of what this package is for: one returns path data, the
other returns a whole document. Both start from a font file.

## Draw a word

```php
use Atelier\Text\SvgText;

$svg = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2')
    ->svg('Atelier', size: 56);

file_put_contents('word.svg', $svg->toString());
```

<img src="images/outline-word.svg" alt="The word Atelier drawn as filled glyph outlines">

Seven glyphs, each one a path. Nothing in that file refers to a font, so it
renders the same on a machine that has never heard of Inter.

## Get the path data instead

When the text has to sit inside a drawing you are already building, take the
outline rather than the document:

```php
$path = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2')
    ->path('Atelier', size: 56, baselineY: 64);

echo '<path d="'.htmlspecialchars($path->d(), \ENT_QUOTES).'"/>';
```

`path()` returns a [`TextPath`](outlines.md): `d()` is the combined outline of
the whole run, the `$advanceWidth` property is how far the run advances, and
`isEmpty()` says whether anything came back.

The difference between the two calls is placement. `path()` takes `x` and
`baselineY` because you are positioning it yourself. `svg()` sizes a document
around the run and needs neither.

## Where to go next

| If you need to | Read |
|---|---|
| pick a font from a directory instead of a path | [Loading a font](fonts.md) |
| change spacing, or place the run yourself | [Path data](outlines.md) |
| address one glyph from CSS or an animation | [SVG documents](documents.md) |
| understand what the mapper will not do | [Shaping](shaping.md) |
| handle a missing glyph | [Errors](errors.md) |

## A runnable example

```bash
php examples/woff2-demo.php
```

It writes `examples/output/woff2/index.html` from the bundled open-source Inter
fixture, so it runs with no font of your own.
