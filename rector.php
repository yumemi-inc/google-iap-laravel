<?php

declare(strict_types=1);

use Quartetcom\StaticAnalysisKit\Rector\Config;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\Php80\Rector\FunctionLike\UnionTypesRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamAnnotationIncorrectNullableRector;

return static function (RectorConfig $rectorConfig): void {
    Config::use($rectorConfig);

    $rectorConfig->paths(array_map(fn (string $path) => __DIR__ . $path, [
        '/src',
        '/tests',
    ]));

    $rectorConfig->skip([
        RemoveUselessReturnTagRector::class,
        UnionTypesRector::class,
        ParamAnnotationIncorrectNullableRector::class => [
            __DIR__ . '/src/Internal/Assert.php',
        ],
    ]);
};
