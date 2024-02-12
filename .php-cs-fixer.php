<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        //'date_time_immutable' => true, // only immutable
        'blank_line_before_statement' => [
            'statements' => [], // new line before return
        ],
        'phpdoc_separation' => false, // new line after group params
        'phpdoc_to_comment' => [
            'ignored_tags' => [
                'psalm-suppress', /** @psalm-suppress */
            ],
        ],
    ])
    ->setFinder($finder)
    ->setLineEnding("\n")
;
