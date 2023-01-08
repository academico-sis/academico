<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->sets([
        SetList::DEAD_CODE,
        LaravelSetList::LARAVEL_90,
        SetList::CODE_QUALITY,
        SetList::PHP_82,
    ]);
};
