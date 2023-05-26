<?php

declare(strict_types=1);

use Quartetcom\StaticAnalysisKit\Rector\Config;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamAnnotationIncorrectNullableRector;

return static function (RectorConfig $rectorConfig): void {
    Config::use($rectorConfig);

    $rectorConfig->paths(array_map(fn (string $path) => __DIR__ . $path, [
        '/src',
        '/tests',
    ]));

    $rectorConfig->skip([
        ParamAnnotationIncorrectNullableRector::class => [
            __DIR__ . '/src/Internal/Assert.php',
        ],
    ]);
};
