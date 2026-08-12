# atelier/text

`atelier/text` maps Unicode to positioned font glyphs and returns their outlines
as SVG path data. It reads a font file and emits geometry; it does not render
text, and it does not depend on any font being installed where the result is
opened.

```
'Atelier' + a font file
   -> TextShaperInterface     one cmap lookup per codepoint, left to right
   -> GlyphRun                positioned glyphs with advances and offsets
   -> OutlinerInterface       each glyph's contours, placed
   -> TextPath                combined path data, or an atelier/svg document
```

Runtime dependencies are `alto/font` for parsing and discovery, `atelier/svg`
for document construction, and `ext-mbstring`.

## Documentation

- [Installation](installation.md): requirements, and the Brotli decoder WOFF2 needs.
- [Getting started](getting-started.md): a first outline, and a first document.
- [Loading a font](fonts.md): from a path, from a query, and which face.
- [Path data](outlines.md): `TextPath`, placement, and spacing.
- [SVG documents](documents.md): one group per glyph, and addressing a single letter.
- [Shaping](shaping.md): what the built-in mapper does, what it refuses, and where to replace it.
- [Errors](errors.md): the exception hierarchy, and what each one means.

## What it does not do

The built-in mapper performs one direct `cmap` lookup per codepoint and returns
a left-to-right run. No font fallback, no bidirectional text, no ligatures, no
kerning, no script shaping.

That is a boundary rather than a gap. Everything downstream consumes a
`GlyphRun`, so a real shaping engine replaces the mapper without touching the
outliner, the generators, or the document builder. See
[Shaping](shaping.md).
