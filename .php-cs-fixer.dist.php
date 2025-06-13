<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->exclude('var')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_var_without_name' => false,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_types_order' => true,
        'phpdoc_order' => true,
        'ordered_imports' => true,
        'heredoc_to_nowdoc' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true
        ],
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache');
