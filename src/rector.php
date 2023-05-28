<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return static function (RectorConfig $rectorConfig): void {
    error_reporting(E_ALL ^ E_DEPRECATED);
    $rectorConfig->importNames();

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/vendor/autoload.php',
    ]);

    $rectorConfig->autoloadPaths([]);

    $rectorConfig->paths([
        __DIR__ . '/common',
    ]);

    // is there a file you need to skip?
    $rectorConfig->skip([
        __DIR__ . '/tests/runtime/*',

        __DIR__ . '/data/*',
        __DIR__ . '/runtime/*',
        __DIR__ . '/vendor/*',

        // not used rules
        AddLiteralSeparatorToNumberRector::class,
        ReturnNeverTypeRector::class,
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ]);

    $rectorConfig->disableParallel();
    $rectorConfig->parallel(120, 6, 10);
};
