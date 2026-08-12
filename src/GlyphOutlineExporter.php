<?php

declare(strict_types=1);

namespace Atelier\Text;

use Alto\Font\Exception\InvalidFontException;
use Alto\Font\Glyph\Contour;
use Alto\Font\Glyph\GlyphOutline;
use Alto\Font\Glyph\PathCommand;

/**
 * @internal prefer Outline\OutlinerInterface for consumer code
 *
 * alto/font's GlyphOutline/Contour/PathCommand only carry generic geometry
 * (font-unit coordinates, a transform() helper) - they know nothing about SVG
 * output. This class owns the scaling, baseline placement, Y-axis flip
 * (font design space is Y-up, SVG is Y-down), and SVG path-data formatting.
 */
final class GlyphOutlineExporter
{
    public static function toPathData(
        GlyphOutline $outline,
        int $unitsPerEm,
        float $fontSize = 1.0,
        float $x = 0.0,
        float $baselineY = 0.0,
    ): string {
        if ($unitsPerEm <= 0) {
            throw new InvalidFontException(\sprintf('Units per em must be positive, got %d.', $unitsPerEm));
        }

        $scale = $fontSize / $unitsPerEm;
        $paths = [];

        foreach ($outline->contours as $contour) {
            $paths[] = self::contourToPathData($contour, $scale, $x, $baselineY);
        }

        return implode(' ', array_filter($paths, static fn (string $path): bool => '' !== $path));
    }

    private static function contourToPathData(Contour $contour, float $scale, float $x, float $baselineY): string
    {
        return implode(' ', array_map(
            static fn (PathCommand $command): string => self::commandToPathData($command, $scale, $x, $baselineY),
            $contour->commands,
        ));
    }

    private static function commandToPathData(PathCommand $command, float $scale, float $x, float $baselineY): string
    {
        $coordinates = self::scaledCoordinates($command, $scale, $x, $baselineY);

        return match ($command->type) {
            'M', 'L' => $command->type.' '.self::format($coordinates[0]).' '.self::format($coordinates[1]),
            'Q' => 'Q '.self::format($coordinates[0]).' '.self::format($coordinates[1]).' '.self::format($coordinates[2]).' '.self::format($coordinates[3]),
            'Z' => 'Z',
            default => throw new InvalidFontException(\sprintf('Unsupported path command "%s".', $command->type)),
        };
    }

    /**
     * @return list<float>
     */
    private static function scaledCoordinates(PathCommand $command, float $scale, float $x, float $baselineY): array
    {
        $scaled = [];

        for ($i = 0; $i < \count($command->coordinates); $i += 2) {
            $scaled[] = $x + $command->coordinates[$i] * $scale;
            $scaled[] = $baselineY - $command->coordinates[$i + 1] * $scale;
        }

        return $scaled;
    }

    private static function format(float $value): string
    {
        $rounded = round($value, 4);

        if (0.0 === $rounded) {
            return '0';
        }

        $formatted = rtrim(rtrim(sprintf('%.4F', $rounded), '0'), '.');

        return '-0' === $formatted ? '0' : $formatted;
    }
}
