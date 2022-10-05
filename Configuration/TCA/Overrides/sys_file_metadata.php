<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

// We only want to have file relations if extension File advanced metadata is loaded.
if (ExtensionManagementUtility::isLoaded('filemetadata')) {
    $configuration = '--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tab.relations, related_files';
    ExtensionManagementUtility::addToAllTCAtypes('sys_file_metadata', $configuration);
}

$tca = [
    'ctrl' => [
        'default_sortby' => 'ORDER BY uid DESC',
        'searchFields' => 'uid,extension,name', // sys_file_metadata.title,sys_file_metadata.keywords,
    ],
    'columns' => [
        'fileinfo' => [
            'config' => [
                'type' => 'user',
                'renderType' => 'findUploader',
            ],
        ],
        'related_files' => [
            'displayCond' => 'FIELD:sys_language_uid:<=:0',
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.relations',
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                'related_files',
                [],
                ''
            ),
        ],
    ],
];
ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file_metadata'], $tca);
