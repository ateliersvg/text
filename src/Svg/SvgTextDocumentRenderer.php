<?php

declare(strict_types=1);

namespace Atelier\Text\Svg;

use Atelier\Svg\Document;
use Atelier\Svg\Element\PathElement;
use Atelier\Svg\Element\Structural\GroupElement;
use Atelier\Svg\Element\SvgElement;
use Atelier\Svg\Svg;
use Atelier\Text\Outline\OutlinedTextRun;

/**
 * @internal renders already-computed outlined glyphs into an Atelier SVG document
 */
final readonly class SvgTextDocumentRenderer
{
    public function render(OutlinedTextRun $run, SvgTextBox $box, ?string $idPrefix = null): Svg
    {
        $root = new SvgElement();
        $root->setWidth($box->width);
        $root->setHeight($box->height);
        $root->setViewbox("0 0 {$box->width} {$box->height}");

        $textGroup = new GroupElement();
        $textGroup->setAttribute('fill', '#000000');

        if (null !== $idPrefix) {
            $textGroup->setAttribute('id', "{$idPrefix}-text");
        }

        foreach ($run->glyphs as $i => $glyph) {
            $char = null !== $glyph->positionedGlyph->codepoint
                ? mb_chr($glyph->positionedGlyph->codepoint)
                : '';

            $glyphGroup = new GroupElement();
            $glyphGroup->setAttribute('class', 'glyph');
            $glyphGroup->setAttribute('data-char', $char);

            if (null !== $idPrefix) {
                $glyphGroup->setAttribute('id', "{$idPrefix}-glyph-{$i}");
            }

            $path = new PathElement();
            $path->setD($glyph->pathData);

            $glyphGroup->appendChild($path);
            $textGroup->appendChild($glyphGroup);
        }

        $root->appendChild($textGroup);

        return Svg::fromDocument(new Document($root));
    }
}
