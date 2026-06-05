<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->exclude([
        'Resources',
        'Tests',
        'DependencyInjection/Configuration.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                              => true,
        '@Symfony'                            => true,
        // Imports
        'ordered_imports'                     => ['sort_algorithm' => 'alpha'],
        'no_unused_imports'                   => true,
        'single_line_after_imports'           => true,
        // Whitespace / formatting
        'array_syntax'                        => ['syntax' => 'short'],
        'no_trailing_whitespace'              => true,
        'no_whitespace_in_blank_line'         => true,
        'blank_line_after_namespace'          => true,
        'blank_line_after_opening_tag'        => true,
        'no_extra_blank_lines'                => ['tokens' => ['extra', 'throw', 'use']],
        // Concise syntax
        'single_quote'                        => true,
        'no_useless_else'                     => true,
        'no_useless_return'                   => true,
        'concat_space'                        => ['spacing' => 'one'],
        // PHPDoc cleanup (safe)
        'phpdoc_align'                        => false, // keep manual alignment
        'phpdoc_no_useless_inheritdoc'        => true,
        'phpdoc_separation'                   => false, // avoid noise
        'phpdoc_trim'                         => true,
        'phpdoc_summary'                      => false,
        // Symfony has these but we relax:
        'yoda_style'                          => false,
        'increment_style'                     => false,
        'native_function_invocation'          => false,
        'php_unit_method_casing'              => false,
        // NOT enabling declare_strict_types globally yet (risky)
        'declare_strict_types'                => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/cache/.php-cs-fixer.cache');