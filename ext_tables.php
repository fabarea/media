<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/File.php');

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
?>