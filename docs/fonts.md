---
order: 30
---
# Loading A Font

Every call starts from a font, because a glyph outline only exists inside one.
There are three ways in, and they differ in who chooses the file.

## From a path

```php
use Atelier\Text\SvgText;

$text = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2');
```

You name the file. This is the right form when the font ships with your
application, which is the common case for a logo, a chart label, or a social
card.

`fromFile()` accepts a string or any `\Stringable`, and every format the
installed `alto/font` supports.

## From a collection

A `.ttc` or `.otc` file holds several faces in one file. The second argument
picks one:

```php
$bold = SvgText::fromFile(__DIR__.'/fonts/Family.ttc', faceIndex: 1);
```

The index is the face's position in the collection, counted from zero. Passing
an index a single-face file does not have is an error, not a fallback.

## From a query

When the font is chosen at runtime rather than written into the code, hand a
finder a description of what you want:

```php
use Alto\Font\FontFinder;
use Alto\Font\FontQuery;
use Atelier\Text\SvgText;

$finder = FontFinder::fromDirectories(__DIR__.'/fonts');

$heading = SvgText::fromFinder($finder, FontQuery::family('Inter')->weight(700));
```

`fromFinder()` also takes the short form, where the family is a string and the
weight and style are separate arguments:

```php
use Alto\Font\Descriptor\FontStyle;

$italic = SvgText::fromFinder($finder, 'Inter', weight: 400, style: FontStyle::Italic);
```

Both forms are the same call. Use the query object when the criteria grow past
a family and a weight.

## Reuse the instance

`SvgText` holds the parsed font. Parsing is the expensive part, so build it once
and call it as often as you need:

```php
$text = SvgText::fromFile(__DIR__.'/fonts/Inter-Regular.woff2');

foreach ($labels as $label) {
    $paths[] = $text->path($label, size: 14)->d();
}
```

`font()` hands back the underlying `Alto\Font\Font` when you need something the
facade does not expose, such as the family name or the units per em.

Next: [Path data](outlines.md).
