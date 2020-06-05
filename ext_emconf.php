<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media management',
    'description' => 'Media management system for TYPO3 CMS.',
    'category' => 'module',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '5.1.0',
    'autoload' => [
        'psr-4' => ['Fab\\Media\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '9.5.0-9.5.99',
                    'vidi' => '4.0.0-0.0.0',
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
