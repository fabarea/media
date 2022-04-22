<?php
use TYPO3\CMS\Core\Utility\ArrayUtility;
if (!defined('TYPO3')) die ('Access denied.');

$tca = [
    'ctrl' => [
        'rootLevel' => -1, // Otherwise File Reference will not work between files.
    ],
];

// Disable the File Upload in IRRE since it can not be configured the target storage.
$GLOBALS['TCA']['sys_file_reference']['columns']['uid_local']['config']['appearance']['fileUploadAllowed'] = false;

ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file_reference'], $tca);
