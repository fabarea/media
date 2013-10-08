<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
//				'Asset' => 'list, listRow, new, create, delete, edit, update, download, upload, linkCreator, imageEditor, massDelete',
//				'Migration' => 'index, migrate, reset',
				'Tool' => 'index, checkStatus',
				'Asset' => 'download, upload, linkCreator, imageEditor, delete, massDelete',
				'Variant' => 'upload',
			),
			array(
				'access' => 'user,group',
				'icon' => 'EXT:media/ext_icon.gif',
				'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf',
				'hideInMenu' => FALSE,
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
			array(
				'EXT:media/Resources/Public/JavaScript/JQuery/jquery.fineuploader-3.4.1.js',
				'EXT:media/Resources/Public/JavaScript/Media.js',
			)
		)
		->addStyleSheetFiles(
			array('EXT:media/Resources/Public/StyleSheets/FileUploader/fineuploader.css')
		)
		->setDefaultPid($configuration['default_pid']['value'])
		->setHeaderComponentsTopLeft(
			array('TYPO3\CMS\Media\ViewHelpers\Component\MenuStorageViewHelper')
		)
		->setHeaderComponentsTopRight(
			array('TYPO3\CMS\Media\ViewHelpers\Component\ButtonToolModuleViewHelper')
		)
		->setHeaderComponentsBottomLeft(
			array('TYPO3\CMS\Media\ViewHelpers\Component\ButtonUploadModuleViewHelper')
		)
		->setBodyComponentsBottom(
			array(
				'TYPO3\CMS\Media\ViewHelpers\Component\PluginLinkCreatorViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\PluginImageEditorViewHelper',
			)
		)
		->setGridComponentsButtons(
			array(
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonLinkCreatorViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonImageEditorViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\ButtonEditViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\ButtonDeleteViewHelper',
			)
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
//		'Asset' => 'list, listRow, new, create, delete, edit, update, download, upload, linkCreator, imageEditor, massDelete',
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
		'variant' => ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image.png',
		'variants' => ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/images.png',
		'variant-edit' => ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_edit.png',
		'variant-link' => ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_link.png',
	),
	$_EXTKEY
);

// Add Media folder type and icon
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
	'pages',
	'contains-media', ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

// Add module icon for Folder
$TCA['pages']['columns']['module']['config']['items'][] = array(
	'Media',
	'media',
	ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
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
ExtensionManagementUtility::makeCategorizable('media', 'sys_file', 'categories', $options);

// Remove edit wizard because it's not working with the TCA tree
unset($TCA['sys_file']['columns']['categories']['config']['wizards']['edit']);
?>