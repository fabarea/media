<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		$_EXTKEY,
		'user', // Make media module a submodule of 'user'
		'm1',
		'bottom', // Position
		array(
			'Asset' => 'list, listRow, new, create, delete, edit, update, download, upload, linkMaker, imageMaker, massDelete',
			'Migration' => 'index, migrate, reset',
			'Tool' => 'index, checkIndex, deleteFiles',
			'Variant' => 'upload',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:media/ext_icon.gif',
			'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
		)
	);
}

// Add sprite icon for type Variant
//\TYPO3\CMS\Backend\Sprite\SpriteManager::addIconSprite()
\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(
	array(
		'variant' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image.png',
		'variants' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/images.png',
		'variant-edit' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_edit.png',
		'variant-link' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_link.png',
	),
	$_EXTKEY
);

// Add Media folder type and icon
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
	'pages',
	'contains-media', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

// Add module icon for Folder
$TCA['pages']['columns']['module']['config']['items'][] = array(
	'Media',
	'media',
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

// Get a comma-separated list of all Media folders
$categoryFolderPidList = \TYPO3\CMS\Media\Utility\MediaFolder::getCategoryFolders();
$options = array();
if ($categoryFolderPidList) {
	// add categorization to all media types
	$options['fieldList'] = '--div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category, categories';
	$options['fieldConfiguration']['foreign_table_where'] = ' AND sys_category.pid IN (' . $categoryFolderPidList . ') ORDER BY sys_category.title ASC';
}

// @todo open issue on to make category 'l10n_mode' => 'exclude' forge.typo3.org/projects/typo3v4-core/issues
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable('media', 'sys_file', 'categories', $options);

// Remove edit wizard because it's not working with the TCA tree
unset($TCA['sys_file']['columns']['categories']['config']['wizards']['edit']);
?>