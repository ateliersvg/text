<?php

declare(strict_types=1);

use Atelier\Text\SvgText;

require __DIR__.'/../vendor/autoload.php';

$font = __DIR__.'/assets/fonts/inter-latin.woff2';
$output = __DIR__.'/output/woff2';
$text = 'Abc deF 01 23 //';
$path = SvgText::fromFile($font)->path($text, size: 72, baselineY: 84);
$viewBoxWidth = max(720, (int) ceil($path->advanceWidth + 36));

if (!is_dir($output) && !mkdir($output, 0777, true) && !is_dir($output)) {
    throw new RuntimeException(\sprintf('Could not create output directory "%s".', $output));
}

$escapedText = htmlspecialchars($text, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
$escapedPath = htmlspecialchars($path->d(), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

$html = <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atelier Text WOFF2 demo</title>
    <style>
        @font-face {
            font-family: "Inter Demo";
            src: url("../../assets/fonts/inter-latin.woff2") format("woff2");
            font-weight: 400;
            font-style: normal;
        }

        :root {
            color-scheme: light;
            font-family: system-ui, sans-serif;
            color: #111827;
            background: #f7f7f2;
        }

        body {
            margin: 0;
        }

        main {
            max-width: 980px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        .specimen {
            margin-top: 24px;
            padding: 24px;
            border: 1px solid #d9d9d0;
            background: #fff;
            overflow-x: auto;
        }

        .label {
            margin: 0 0 10px;
            color: #4b5563;
            font-size: 13px;
        }

        .css-text {
            font: 72px/1.1 "Inter Demo", sans-serif;
            white-space: nowrap;
        }

        svg {
            display: block;
            min-width: 720px;
            width: 100%;
            height: 128px;
        }
    </style>
</head>
<body>
    <main>
        <h1>WOFF2 to SVG paths</h1>
        <section class="specimen">
            <p class="label">Browser text</p>
            <div class="css-text">{$escapedText}</div>
        </section>
        <section class="specimen">
            <p class="label">Generated SVG path</p>
            <svg viewBox="0 -10 {$viewBoxWidth} 128" role="img" aria-label="Inter rendered as SVG paths" xmlns="http://www.w3.org/2000/svg">
                <path d="{$escapedPath}"/>
            </svg>
        </section>
    </main>
</body>
</html>
HTML;

file_put_contents($output.'/index.html', $html);

echo "Generated examples/output/woff2/index.html\n";
