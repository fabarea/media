<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media management',
    'description' => 'Media management system for TYPO3 CMS.',
    'category' => 'module',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '4.5.0-dev',
    'autoload' => [
        'psr-4' => ['Fab\\Media\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '7.6.0-8.99.99',
                    'vidi' => '2.3.1-2.99.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                    'metadata' => '',
                    'filemetadata' => '',
                ],
        ]
];
