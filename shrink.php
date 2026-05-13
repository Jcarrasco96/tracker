#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use JShrink\Minifier;
//use tubalmartin\CssMin\Minifier as CssMinifier;

function color(string $text, string $color = 'default'): string
{
    $colors = [
        'default' => "\e[0m",
        'green' => "\e[32m",
        'red' => "\e[31m",
        'yellow' => "\e[33m",
        'blue' => "\e[34m",
        'magenta' => "\e[35m",
        'cyan' => "\e[36m",
    ];
    return ($colors[$color] ?? $colors['default']) . $text . $colors['default'];
}

$jsFiles = [
    'assets/js/views/index.js',
    'assets/js/fix.container.js',
    'assets/js/index.js',
    'assets/js/notify.js',
];
$cssFiles = [
//    'assets/css/animate.css',
//    'assets/css/preloader.css',
//    'assets/css/style.css',
];

echo color("\n=== Minifying JavaScript ===\n", 'cyan');

foreach ($jsFiles as $src) {
    $dest = preg_replace('/\.js$/i', '.min.js', $src);

    if (is_file($dest) && filemtime($dest) >= filemtime($src)) {
        echo color("[SKIP] ", 'yellow') . "$src (already minified)\n";
        continue;
    }

    try {
        $content = file_get_contents($src);
        $minified = Minifier::minify($content, ['flaggedComments' => false]);

        $banner = "/* " . basename($src) . " - minified " . date('Y-m-d H:i:s') . " */\n";
        file_put_contents($dest, $banner . $minified);

        echo color("[OK]   ", 'green') . "$src → $dest\n";
    } catch (Throwable $e) {
        echo color("[ERROR] ", 'red') . "$src – {$e->getMessage()}\n";
    }
}

//echo color("\n=== Minifying CSS ===\n", 'cyan');

//foreach ($cssFiles as $src) {
//    $dest = preg_replace('/\.css$/i', '.min.css', $src);
//
//    if (is_file($dest) && filemtime($dest) >= filemtime($src)) {
//        echo color("[SKIP] ", 'yellow') . "$src (already minified)\n";
//        continue;
//    }
//
//    try {
//        $content = file_get_contents($src);
//        $minifier = new CssMinifier($content);
//        $minified = $minifier->run();
//
//        $banner = "/* " . basename($src) . " - minified " . date('Y-m-d H:i:s') . " */\n";
//        file_put_contents($dest, $banner . $minified);
//
//        echo color("[OK]   ", 'green') . "$src → $dest\n";
//    } catch (Throwable $e) {
//        echo color("[ERROR] ", 'red') . "$src – {$e->getMessage()}\n";
//    }
//}

echo color("\n=== Summary ===\n", 'magenta');

echo "JS processed: " . count($jsFiles) . "\n";

//echo "CSS processed: " . count($cssFiles) . "\n";

echo color("\n¡Ready!\n", 'green');
