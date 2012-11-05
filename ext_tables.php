<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Media.php');


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
		'tx_media_m1', // Submodule key
		'bottom', // Position
		array(
			'Media' => 'list,listRow,new,create,delete,edit,update',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:media/ext_icon.gif',
			'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
		)
	);
}

?>