<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSkip([
        __DIR__ . '/src/*/DependencyInjection',
        __DIR__ . '/src/*/Resources',
        __DIR__ . '/src/*/Tests',
        __DIR__ . '/src/*/DoctrineMigrations',
        __DIR__ . '/migrations',
    ])
    ->withSets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);