<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

include_once(t3lib_extMgm::extPath($_EXTKEY).'Configuration/TCA/Media.php');

if (TYPO3_MODE === 'BE') {

		// Make sure the class exists to avoid a Runtime Error
	if (class_exists('Tx_Vidi_Service_ModuleLoader')) {
		/** @var Tx_Vidi_Service_ModuleLoader $moduleLoader */
		$moduleLoader = t3lib_div::makeInstance('Tx_Vidi_Service_ModuleLoader', $_EXTKEY);
		$moduleLoader->addTcaBasedTree(
			'sys_category',
			array(
				'parentField' => 'parent',
				'editable'	  => true
			),
			array(
				'*' => array(
					'unique' => false,
					'foreignField' => 'sys_category',
					'MM' => 'sys_category_record_mm',
					'MM_opposite_field' => 'items',
				)
			)
		);
		$moduleLoader->setAllowedDataTypes(array('sys_file'));
		$moduleLoader->setDdInterface('Tx_Taxonomy_Service_Vidi_DragAndDropHandler');
		$moduleLoader->setGridService('sys_file', 'Tx_Media_Service_Vidi_GridData');
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