<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media management',
    'description' => 'Media management system for TYPO3 CMS.',
    'category' => 'module',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '6.0.1',
    'autoload' => [
        'psr-4' => ['Fab\\Media\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '11.5.0-11.5.99',
                    'vidi' => '5.0.0-0.0.0',
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
