<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	// Make sure the class exists to avoid a Runtime Error
	if (class_exists('Tx_Vidi_Service_ModuleLoader')) {
		/** @var Tx_Vidi_Service_ModuleLoader $moduleLoader */
		$moduleLoader = t3lib_div::makeInstance('Tx_Vidi_Service_ModuleLoader', $_EXTKEY);
		$moduleLoader->addStandardTree(Tx_Vidi_Service_ModuleLoader::TREE_FILES);
		$moduleLoader->setAllowedDataTypes(array('sys_file'));
		$moduleLoader->setMainModule('file');
		$moduleLoader->addJavaScriptFiles(array(
				'ListModifications/CustomGridToolbar.js',
				'ModuleConfiguration.js'
			),
			'Resources/Public/JavaScript'
		);
		$moduleLoader->setModuleLanguageFile('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_media.xml');
		$moduleLoader->setIcon('EXT:' . $_EXTKEY . '/ext_icon.gif');
		$moduleLoader->register();
	}
}

?>