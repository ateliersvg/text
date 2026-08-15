---
order: 60
---
# Shaping

Shaping is the step between a string and a set of positioned glyphs: which
glyph each character maps to, and where each one sits. This package provides a
deliberately limited mapper and an interface for replacing it with a full
shaping engine.

## What the built-in mapper does

One direct `cmap` lookup per Unicode codepoint, producing a left-to-right run.
Each glyph gets its advance from the font's metrics, plus the configured
`letterSpacing` and `wordSpacing`.

That is the whole algorithm. It is enough for a headline, a chart label, a
watermark, or a social card in a Latin script, which is what the package was
built for.

## What it does not do

| Missing | What it would need |
|---|---|
| font fallback | a font stack and a policy for choosing within it |
| bidirectional text | the Unicode bidirectional algorithm |
| ligatures | reading `GSUB` from the font |
| kerning | reading `GPOS` or `kern` |
| script shaping | per-script rules for Arabic, Devanagari, and others |

Complex scripts, bidirectional text, ligatures, kerning, and font fallback
require a full shaping engine such as HarfBuzz. The built-in mapper keeps its
scope explicit instead of approximating those behaviors.

A codepoint the font has no glyph for throws
[`MissingGlyphException`](errors.md) rather than falling back, because falling
back means choosing another font, and this package has no opinion about which.

## The seam

`Shaping\TextShaperInterface` is one method:

```php
interface TextShaperInterface
{
    public function shape(string $text): GlyphRun;
}
```

Everything downstream consumes the `GlyphRun` it returns. The outliner, the
generators, and the document builder never see the string, so replacing the
mapper replaces the shaping and nothing else.

## What a shaper returns

`GlyphRun` holds an array of `PositionedGlyph` and a `TextDirection`:

```php
final class GlyphRun
{
    public array $glyphs;            // list<PositionedGlyph>
    public TextDirection $direction; // LeftToRight or RightToLeft
}
```

Each glyph carries where it came from and where it goes:

| Property | Type | What it is |
|---|---|---|
| `$glyphId` | `Alto\Font\Glyph\GlyphId` | the glyph in the font, not the character |
| `$metrics` | `Alto\Font\Glyph\GlyphMetrics` | the font's own advance and bearings |
| `$cluster` | `int` | which part of the source string produced it |
| `$xAdvance`, `$yAdvance` | `float` | how far the pen moves after drawing |
| `$xOffset`, `$yOffset` | `float` | how far this glyph shifts from the pen |
| `$codepoint` | `?int` | the source codepoint, or null when several produced one glyph |

`$cluster` and the nullable `$codepoint` are what make a real shaper possible:
a ligature is one glyph from two characters, so it has one cluster and no single
codepoint. The built-in mapper never produces that case, but the contract
already allows it.

`TextDirection::RightToLeft` is available to custom shapers. The built-in mapper
returns `TextDirection::LeftToRight`.

## Replacing the mapper

Implement the interface, and hand your run to the rest of the pipeline:

```php
use Atelier\Text\Shaping\GlyphRun;
use Atelier\Text\Shaping\TextShaperInterface;

final class HarfBuzzShaper implements TextShaperInterface
{
    public function shape(string $text): GlyphRun
    {
        // Call out to a shaping engine, then build PositionedGlyph values
        // from what it returns.
    }
}
```

The outline side has the same shape: `Outline\OutlinerInterface` takes a
`FontText` and returns a `TextPath`, so a different way of turning glyphs into
contours plugs in the same way.

Next: [Errors](errors.md).
