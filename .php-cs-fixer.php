<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'var',
        'config',
        'public',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'single_line_throw' => false,
        'single_line_comment_spacing' => false,
        'declare_strict_types' => true,
        'date_time_immutable' => true, // only immutable
        'blank_line_before_statement' => [
            'statements' => [
                'return', // new line before return
            ],
        ],
        //'phpdoc_separation' => false, // new line after group params
        'phpdoc_to_comment' => [
            'ignored_tags' => [
                'var', /** @var */
                'see',
                'psalm-suppress',
                'phpstan-ignore',
            ],
        ],
    ])
    ->setFinder($finder)
    ->setLineEnding("\n")
    ->setRiskyAllowed(true) // for declare_strict_types | date_time_immutable
;
