---
order: 50
---
# SVG Documents

`svg()` returns an `atelier/svg` document rather than a string of path data. Use
it when the text is the drawing, and when you want to reach one glyph rather
than the run as a whole.

```php
use Atelier\Text\SvgText;

$svg = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2')
    ->svg('Atelier', size: 56);

echo $svg->toPrettyString();
```

The document is sized around the run, so `path()`'s `x` and `baselineY` have no
equivalent here: there is nothing to position it against.

## One group per glyph

Every glyph gets its own group, carrying the character it came from:

```xml
<g class="glyph" data-char="A"><path d="M 11.2 71.6 ..."/></g>
<g class="glyph" data-char="t"><path d="M 52.4 71.6 ..."/></g>
```

<img src="images/glyph-groups.svg" alt="The word glyphs with every other letter drawn at lower opacity, showing the grouping">

Alternate letters are dimmed above, which is a stylesheet reaching one group at
a time. Nothing in the package does that; it is what the grouping makes
possible.

## Addressing a single glyph

Groups carry no `id` by default, because an id that is not unique across a page
is worse than none. Pass `idPrefix` when you need them:

```php
$svg = $text->svg('Atelier', size: 56, idPrefix: 'headline');
```

That produces `headline-text` on the wrapper and `headline-glyph-0`,
`headline-glyph-1`, and so on, numbered from zero in reading order.

Deterministic ids are what an animation needs:

```css
#headline-glyph-0 { animation: rise 400ms ease-out both; }
#headline-glyph-1 { animation: rise 400ms ease-out 60ms both; }
```

Use one prefix per run on a page. Two runs sharing a prefix produce two
`glyph-0` ids, and the second one wins wherever a selector looks.

## Spacing works the same

`letterSpacing` and `wordSpacing` behave exactly as they do on `path()`, and the
document widens to fit:

```php
$text->svg('Atelier', size: 56, letterSpacing: 4);
```

See [Path data](outlines.md) for what those two arguments measure.

## Composing into a larger document

The return is a real `Atelier\Svg\Svg`, so everything that package offers
applies: query it, optimize it, sanitize it, or serialize it.

```php
$svg = $text->svg('Atelier', size: 56);

$svg->optimizeWeb()->save('headline.svg');
```

When the text has to sit inside a document you already hold, take the path data
instead and place it yourself. See [Path data](outlines.md).

Next: [Shaping](shaping.md).
