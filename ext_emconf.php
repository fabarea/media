<?php

$EM_CONF[$_EXTKEY] = [
  'title' => 'Media management',
  'description' => 'Media management system for TYPO3 CMS.',
  'category' => 'module',
  'author' => 'Fabien Udriot',
  'author_email' => 'fabien.udriot@typo3.org',
  'state' => 'stable',
  'version' => '4.1.0-dev',
  'constraints' =>
  [
    'depends' =>
    [
      'typo3' => '7.6.0-7.99.99',
      'vidi' => '2.0.0-2.99.99',
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
