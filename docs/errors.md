---
order: 70
---
# Errors

Every exception this package throws implements
`Atelier\Text\Exception\ExceptionInterface`, so one catch covers it.

```php
use Atelier\Text\Exception\ExceptionInterface;
use Atelier\Text\SvgText;

try {
    echo SvgText::fromFile($fontPath)->path($label, size: 24)->d();
} catch (ExceptionInterface $e) {
    // A codepoint the font has no glyph for, or an argument that is not finite.
}
```

Two concrete types sit under it.

## MissingGlyphException

Thrown when the font has no glyph for a codepoint in your text. The message
names the codepoint:

```
Font has no glyph for codepoint U+6F22.
```

This is a deliberate failure rather than a blank or a substitution box. The
package does no font fallback, so it cannot quietly pick another face, and
rendering nothing where a character was asked for would hide the problem until
someone looked at the output.

Catch it when the text is not yours to control:

```php
use Atelier\Text\Exception\MissingGlyphException;

try {
    $path = $text->path($userInput, size: 24);
} catch (MissingGlyphException $e) {
    // Fall back to a font with wider coverage, or refuse the input.
}
```

The fallback is yours to write, because only you know which font to reach for.
See [Shaping](shaping.md) for why the package does not choose one.

## InvalidArgumentException

Thrown when a number that must be finite is not. It extends the SPL
`InvalidArgumentException`, so existing handlers still catch it.

| Argument | Rule | Message |
|---|---|---|
| `size` | finite and greater than zero | `Text size must be finite and greater than zero.` |
| `baselineY` | finite | `Text baseline must be finite.` |
| `x` | finite | `Text x coordinate must be finite.` |
| `letterSpacing` | finite | `Letter spacing must be finite.` |
| `wordSpacing` | finite | `Word spacing must be finite.` |

Zero, a negative, `INF`, and `NAN` all fail the size check. That matters when a
size comes from a calculation: a division that reaches zero produces an
exception here rather than an invisible glyph run.

## Errors from the font layer

Loading a font that does not exist, or a file that is not a font, throws from
`alto/font` rather than from here:

```
Alto\Font\Exception\InvalidFontException: Font file "/nope.woff2" does not exist.
```

That exception does not implement this package's interface, because the failure
is not this package's. `alto/font` has its own,
`Alto\Font\Exception\FontExceptionInterface`, covering
`InvalidFontException`, `FontNotFoundException`, and
`UnsupportedFontException`. Catch it alongside when you load fonts from paths
you have not verified.

A WOFF2 file with no Brotli decoder available fails the same way. See
[Installation](installation.md).
