<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Extend TCA of File
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/File.php');

$TCA["sys_file_variants"] = array(
	"ctrl" => array(
		'title' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variants',
		'label' => 'role',
		'label_alt' => 'original',
		'label_alt_force' => 'true',
		'rootLevel' => -1,
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/FileVariants.php',
		//'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/Advertise.png',
	),
);

if (TYPO3_MODE == 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		$_EXTKEY,
		'user', // Make media module a submodule of 'user'
		'm1',
		'bottom', // Position
		array(
			'Asset' => 'list, listRow, new, create, delete, edit, update, download, upload, linkMaker, imageMaker',
			'Migration' => 'index, migrate, reset',
			'Tool' => 'index, checkStatus',
			'Variant' => 'upload',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:media/ext_icon.gif',
			'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
		)
	);
}

// media folder type and icon
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
	'pages',
	'contains-media', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

$TCA['pages']['columns']['module']['config']['items'][] = array(
	'Media',
	'media',
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

// Make File aware of the enable fields.
$TCA['sys_file']['ctrl']['tstamp'] = 'tstamp';
$TCA['sys_file']['ctrl']['crdate'] = 'crdate';
$TCA['sys_file']['ctrl']['enablecolumns'] =  array(
	'disabled' => 'hidden',
	'starttime' => 'starttime',
	'endtime' => 'endtime'
);

// get a comma-separated list of all Media folders
$categoryFolderPidList = \TYPO3\CMS\Media\Utility\MediaFolder::getCategoryFolders();
$options = array();
if ($categoryFolderPidList) {
	// add categorization to all media types
	$options['fieldList'] = '--div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category, categories';
	$options['fieldConfiguration']['foreign_table_where'] = ' AND sys_category.pid IN (' . $categoryFolderPidList . ') ORDER BY sys_category.title ASC';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable('media', 'sys_file', 'categories', $options);

// remove edit wizard because it's not working with the TCA tree
unset($TCA['sys_file']['columns']['categories']['config']['wizards']['edit']);
?>