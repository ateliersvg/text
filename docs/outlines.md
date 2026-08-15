---
order: 40
---
# Path Data

`path()` returns the whole run as one outline. Use it when the text belongs
inside a drawing you are already assembling, or when something other than
`atelier/svg` will do the rendering.

```php
use Atelier\Text\SvgText;

$path = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2')
    ->path('Atelier', size: 56, baselineY: 64);

$path->d();             // 'M 11.2 63.6 L 28.1 17 L 35 17 ...'
$path->advanceWidth;    // 170.46
$path->isEmpty();       // false
```

## What comes back

`TextPath` carries three things and nothing else.

| Member | Type | What it is |
|---|---|---|
| `d()` | `string` | every glyph's contours, concatenated, in one `d` attribute |
| `$advanceWidth` | `float` | how far the run advances, including configured spacing |
| `isEmpty()` | `bool` | true when the text produced no glyphs, as an empty string does |

`$advanceWidth` is the value to use when you lay several runs out yourself: it
is where the next one starts, not the ink's bounding box. A run ending in a
space advances past it even though nothing was drawn.

## Placement

| Argument | Default | Effect |
|---|---|---|
| `size` | required | em size, in user units |
| `x` | `0.0` | where the run starts horizontally |
| `baselineY` | `0.0` | the baseline, not the top of the text |

`baselineY` catches people out. It is the line the glyphs sit on, so ascenders
go above it and descenders below. Leaving it at zero puts most of the text at
negative coordinates, above the origin.

```php
// A 56-unit run, sitting on a baseline 64 units down.
$path = $text->path('Atelier', size: 56, x: 12, baselineY: 64);
```

## Spacing

Two arguments change the gaps, and both are in user units rather than ems.

| Argument | Default | Applies to |
|---|---|---|
| `letterSpacing` | `0.0` | between glyphs |
| `wordSpacing` | `0.0` | in addition, at every space |

```php
$text->path('OUTLINE', size: 44, letterSpacing: 6);
```

<img src="images/spacing-none.svg" alt="The word OUTLINE at its natural letter spacing">

<img src="images/spacing-letter.svg" alt="The same word with six units added after every glyph">

The second run advances 227.08 against 191.08: 36 units wider, which is six
units in each of the six gaps between seven glyphs, and nothing after the last.
That width is in `$advanceWidth`, so a layout reading it stays correct.

Word spacing needs more than one word to show anything:

```php
$text->path('one two', size: 44, wordSpacing: 24);
```

<img src="images/spacing-word-none.svg" alt="The words one and two at their natural spacing">

<img src="images/spacing-word.svg" alt="The same words with twenty-four units added at the space">

One space, twenty-four units: 191.19 against 167.19. Word spacing is added on
top of letter spacing rather than instead of it, so setting both widens the gap
at a space twice.

## One glyph can be several contours

The path data of a single glyph is not always one closed shape. Filled, that
composites correctly and nobody notices. Stroked, the construction shows:

<img src="images/outline-contours.svg" alt="The letters A and a drawn as stroked contours, the A showing a separate bar">

The `A` comes back as its envelope plus a separate rectangle for the crossbar.
This matters only if you stroke the output instead of filling it: use
`fill-rule="nonzero"`, which is the default, and fill it.

Next: [SVG documents](documents.md).
