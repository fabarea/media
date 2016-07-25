<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');


/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
$configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
$configuration = $configurationUtility->getCurrentConfiguration('media');

$tca = [
    'types' => [
        0 => ['showitem' => $GLOBALS['TCA']['sys_file_storage']['types'][0]['showitem'] . ',

			--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tab.upload_settings,
			maximum_dimension_original_image, extension_allowed_file_type_1, extension_allowed_file_type_2, extension_allowed_file_type_3, extension_allowed_file_type_4, extension_allowed_file_type_5,
				',
        ],
    ],
    'columns' => [
        'maximum_dimension_original_image' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.maximum_dimension_original_image',
            'config' => [
                'type' => 'input',
                'size' => 24,
                'default' => '1920x1920',
                'eval' => 'trim',
            ],
        ],
        'extension_allowed_file_type_1' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.extension_allowed_file_type_1',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'default' => 'txt, html',
                'eval' => 'trim',
            ],
        ],
        'extension_allowed_file_type_2' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.extension_allowed_file_type_2',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'default' => 'jpg, jpeg, bmp, png, tiff, tif, gif, eps',
                'eval' => 'trim',
            ],
        ],
        'extension_allowed_file_type_3' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.extension_allowed_file_type_3',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'default' => 'mp3, mp4, m4a, wma, f4a',
                'eval' => 'trim',
            ],
        ],
        'extension_allowed_file_type_4' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.extension_allowed_file_type_4',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'default' => 'mov, avi, mpeg, mpg, mp4, m4v, flv, f4v, webm, wmv, ogv, 3gp',
                'eval' => 'trim',
            ],
        ],
        'extension_allowed_file_type_5' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.extension_allowed_file_type_5',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'default' => 'pdf, zip, doc, docx, dotx, ppt, pptx, pps, ppsx, odt, xls, xlsx, xltx, rtf, xlt',
                'eval' => 'trim',
            ],
        ],
    ],
];

$tcaForHiddenFolderTree = [];
if (!$configuration['has_folder_tree']['value']) {
    $tcaForHiddenFolderTree = [

        'types' => [
            0 => ['showitem' => $GLOBALS['TCA']['sys_file_storage']['types'][0]['showitem'] . ',

			--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tab.media_mount_point,
			mount_point_file_type_1, mount_point_file_type_2, mount_point_file_type_3, mount_point_file_type_4, mount_point_file_type_5,

			--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tab.upload_settings,
			maximum_dimension_original_image, extension_allowed_file_type_1, extension_allowed_file_type_2, extension_allowed_file_type_3, extension_allowed_file_type_4, extension_allowed_file_type_5,
				',

            ],
        ],
        'columns' => [
            'mount_point_file_type_1' => [
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.mount_point_file_type_1',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'sys_filemounts',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'wizards' => [
                        'edit' => [
                            'type' => 'popup',
                            'module' => [
                                'name' => 'wizard_edit',
                            ],
                            'icon' => 'edit2.gif',
                            'popup_onlyOpenIfSelected' => 1,
                            'notNewRecords' => 1,
                            'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1,resizable=yes'
                        ],
                    ],
                ],
            ],
            'mount_point_file_type_2' => [
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.mount_point_file_type_2',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'sys_filemounts',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'wizards' => [
                        'edit' => [
                            'type' => 'popup',
                            'module' => [
                                'name' => 'wizard_edit',
                            ],
                            'icon' => 'edit2.gif',
                            'popup_onlyOpenIfSelected' => 1,
                            'notNewRecords' => 1,
                            'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1,resizable=yes'
                        ],
                    ],
                ],
            ],
            'mount_point_file_type_3' => [
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.mount_point_file_type_3',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'sys_filemounts',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'wizards' => [
                        'edit' => [
                            'type' => 'popup',
                            'module' => [
                                'name' => 'wizard_edit',
                            ],
                            'icon' => 'edit2.gif',
                            'popup_onlyOpenIfSelected' => 1,
                            'notNewRecords' => 1,
                            'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1,resizable=yes'
                        ],
                    ],
                ],
            ],
            'mount_point_file_type_4' => [
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.mount_point_file_type_4',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'sys_filemounts',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'wizards' => [
                        'edit' => [
                            'type' => 'popup',
                            'module' => [
                                'name' => 'wizard_edit',
                            ],
                            'icon' => 'edit2.gif',
                            'popup_onlyOpenIfSelected' => 1,
                            'notNewRecords' => 1,
                            'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1,resizable=yes'
                        ],
                    ],
                ],
            ],
            'mount_point_file_type_5' => [
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_storage.mount_point_file_type_5',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'sys_filemounts',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'wizards' => [
                        'edit' => [
                            'type' => 'popup',
                            'module' => [
                                'name' => 'wizard_edit',
                            ],
                            'icon' => 'edit2.gif',
                            'popup_onlyOpenIfSelected' => 1,
                            'notNewRecords' => 1,
                            'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1,resizable=yes'
                        ],
                    ],
                ],
            ],
        ],
    ];
}

// Merge first two possible TCA whether the folder tree is displayed or not.
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($tca, $tcaForHiddenFolderTree);

// Final merge into "sys_file_storage".
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file_storage'], $tca);
