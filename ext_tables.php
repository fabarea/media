<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Frontend',
	'Media Frontend'
);

//$pluginSignature = str_replace('_','',$_EXTKEY) . '_' . frontend;
//$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_' .frontend. '.xml');





if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'media',	// Submodule key
		'',						// Position
		array(
		//	'Media' => 'show, list, new, create, edit, update, delete',
		//	'Collection' => 'list, show, new, create, edit, update, delete',
		//	'Filter' => 'list, show, new, create, edit, update, delete',
		//	'File' => 'show, list, new, create, edit, update, delete',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_media.xml',
		)
	);

	// Make sure the class exists to avoid a Runtime Error
	if (class_exists('Tx_Vidi_Service_ModuleLoader')) {
		/** @var Tx_Vidi_Service_ModuleLoader $moduleLoader */
		$moduleLoader = t3lib_div::makeInstance('Tx_Vidi_Service_ModuleLoader', $_EXTKEY);
		$moduleLoader->addStandardTree(Tx_Vidi_Service_ModuleLoader::TREE_FILES);
		$moduleLoader->setAllowedDataTypes(array('__FILES'));
		$moduleLoader->setMainModule('file');
		$moduleLoader->setModuleLanguageFile('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_media.xml');
		$moduleLoader->setIcon('EXT:' . $_EXTKEY . '/ext_icon.gif');
		$moduleLoader->register();
	}
}


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Media Management');

// Load TCA extension
require_once(t3lib_extMgm::extPath('media') . 'Configuration/TCA/Media.php');
?>