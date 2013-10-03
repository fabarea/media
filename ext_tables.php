<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

	if (TYPO3_MODE == 'BE') {
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
			$_EXTKEY,
			'user', // Make media module a submodule of 'user'
			'm1',
			'bottom', // Position
			array(
//				'Asset' => 'list, listRow, new, create, delete, edit, update, download, upload, linkMaker, imageMaker, massDelete',
//				'Migration' => 'index, migrate, reset',
				'Tool' => 'index, checkStatus',
				'Asset' => 'delete, edit, update, download, upload, linkMaker, imageMaker, massDelete',
				'Variant' => 'upload',
			),
			array(
				'access' => 'user,group',
				'icon' => 'EXT:media/ext_icon.gif',
				'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf',
				'isDisplayed' => FALSE,
			)
		);
	}

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

	/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
	$configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
	$configuration = $configurationUtility->getCurrentConfiguration('vidi');

	/** @var \TYPO3\CMS\Vidi\ModuleLoader $moduleLoader */
	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Vidi\ModuleLoader', 'sys_file');
	$moduleLoader->setIcon('EXT:media/ext_icon.gif')
		->setModuleLanguageFile('LLL:EXT:media/Resources/Private/Language/locallang.xlf')
		->addJavaScriptFiles(
			array('EXT:media/Resources/Public/JavaScript/JQuery/jquery.fineuploader-3.4.1.js')
		)
		->addStyleSheetFiles(
			array('EXT:media/Resources/Public/StyleSheet/FileUploader/fineuploader.css')
		)
		->setDefaultPid($configuration['default_pid']['value'])
		->setDocHeader(
			TYPO3\CMS\Vidi\ModuleLoader::DOC_HEADER_TOP,
			TYPO3\CMS\Vidi\ModuleLoader::DOC_HEADER_LEFT,
			array('TYPO3\CMS\Media\ViewHelpers\DocHeader\MenuStorageViewHelper')
		)
		->setDocHeader(
			TYPO3\CMS\Vidi\ModuleLoader::DOC_HEADER_TOP,
			TYPO3\CMS\Vidi\ModuleLoader::DOC_HEADER_RIGHT,
			array('TYPO3\CMS\Media\ViewHelpers\DocHeader\ButtonToolModuleViewHelper')
		)
		->setDocHeader(
			TYPO3\CMS\Vidi\ModuleLoader::DOC_HEADER_BOTTOM,
			TYPO3\CMS\Vidi\ModuleLoader::DOC_HEADER_LEFT,
			array('TYPO3\CMS\Media\ViewHelpers\DocHeader\ButtonUploadModuleViewHelper')
		)
		->register();

	// Connect "postFileIndex" signal slot with the metadata service.
	/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
	$signalSlotDispatcher = $objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
	$signalSlotDispatcher->connect(
		'TYPO3\CMS\Vidi\Controller\Backend\ContentController',
		'postProcessMatcherObject',
		'TYPO3\CMS\Media\SignalSlot\ContentController',
		'postProcessMatcherObject',
		TRUE
	);

	$controllerActions = array(
//		'Asset' => 'list, listRow, new, create, delete, edit, update, download, upload, linkMaker, imageMaker, massDelete',
//		'Migration' => 'index, migrate, reset',
		'Tool' => 'index, checkIndex, deleteFiles',
//		'Variant' => 'upload',
	);

	/**
	 * Register some controllers for the Backend (Ajax)
	 * Special case for FE User and FE Group
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		$_EXTKEY,
		'Pi1',
		$controllerActions,
		$controllerActions
	);

	\TYPO3\CMS\Vidi\AjaxDispatcher::addAllowedActions(
		$_EXTKEY,
		'Pi1',
		$controllerActions
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
	$options['fieldConfiguration']['foreign_table_where'] = ' AND sys_category.pid IN (' . $categoryFolderPidList . ') AND sys_category.sys_language_uid IN (0,-1) ORDER BY sys_category.title ASC';
}

// @todo open issue on to make category 'l10n_mode' => 'exclude' forge.typo3.org/projects/typo3v4-core/issues
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable('media', 'sys_file', 'categories', $options);

// Remove edit wizard because it's not working with the TCA tree
unset($TCA['sys_file']['columns']['categories']['config']['wizards']['edit']);
?>