<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths(
        [
            __DIR__.'/src',
        ]
    )
    ->withSets(
        [
            SetList::DEAD_CODE,
            SetList::CODE_QUALITY,
            SetList::TYPE_DECLARATION,
            SetList::PRIVATIZATION,
            SetList::EARLY_RETURN,
        ]
    )
    ->withRules(
        [
            DeclareStrictTypesRector::class,
        ]
    )
    ->withPhpSets();
