<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => false,
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same',
        ],
    ])
    ->setFinder($finder);
