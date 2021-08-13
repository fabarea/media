<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media management',
    'description' => 'Media management system for TYPO3 CMS.',
    'category' => 'module',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '6.0.0',
    'autoload' => [
        'psr-4' => ['Fab\\Media\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '10.4.0-10.4.99',
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
