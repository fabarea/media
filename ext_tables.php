<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// USER TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.vidi.enableMediaFilePicker = 1');

if (TYPO3_MODE == 'BE') {

	# Hide the module in the BE.
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.hideModules.user := addToList(MediaM1)');

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		$_EXTKEY,
		'user', // Make media module a submodule of 'user'
		'm1',
		'bottom', // Position
		array(
			'Tool' => 'index, checkIndex, deleteFiles',
			'Asset' => 'show, create, update, move , delete, massDelete',
			'ImageEditor' => 'show',
			'Storage' => 'list',
			'LinkCreator' => 'show',
			'Variant' => 'upload',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:media/ext_icon.gif',
			'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
		)
	);

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
			array(
				'EXT:media/Resources/Public/StyleSheets/media.css',
				'EXT:media/Resources/Public/StyleSheets/FileUploader/fineuploader.css'
			)
		)
		->setNavigationTopLeftComponents(
			array('TYPO3\CMS\Media\ViewHelpers\Component\MenuStorageViewHelper')
		)
		->setNavigationTopRightComponents(
			array('TYPO3\CMS\Media\ViewHelpers\Component\ButtonToolViewHelper')
		)
		->setNavigationBottomLeftComponents(
			array('TYPO3\CMS\Media\ViewHelpers\Component\ButtonUploadViewHelper')
		)
		->setGridTopComponents(
			array('TYPO3\CMS\Media\ViewHelpers\Component\ConfigurationCheckViewHelper')
		)
		->setGridBottomComponents(
			array(
				'TYPO3\CMS\Media\ViewHelpers\Component\PluginLinkCreatorViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\PluginImageEditorViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\PluginFilePickerViewHelper',
			)
		)
		->setGridButtonsComponents(
			array(
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonLinkCreatorViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonImageEditorViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonFilePickerViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\ButtonEditViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonDeleteViewHelper',
			)
		)
		->setGridMenuComponents(
			array(
				'TYPO3\CMS\Media\ViewHelpers\Component\MenuItemFilePickerViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\MenuItemChangeStorageViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\MenuItemMassDeleteViewHelper',
			)
		)
		->register();


	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

	/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
	$signalSlotDispatcher = $objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

	// Connect "postFileIndex" signal slot with the metadata service.
	$signalSlotDispatcher->connect(
		'TYPO3\CMS\Vidi\Controller\Backend\ContentController',
		'postProcessMatcherObject',
		'TYPO3\CMS\Media\SignalSlot\ContentController',
		'postProcessMatcherObject',
		TRUE
	);
}

// Add sprite icon for type Variant
//\TYPO3\CMS\Backend\Sprite\SpriteManager::addIconSprite()
\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(
	array(
		'variant' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image.png',
		'variants' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/images.png',
		'variant-edit' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_edit.png',
		'variant-link' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_link.png',
		'storage-change' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/folder_go.png',
	),
	$_EXTKEY
);

// Add Media folder type and icon
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
	'pages',
	'contains-media', TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

// Add module icon for Folder
$TCA['pages']['columns']['module']['config']['items'][] = array(
	'Media',
	'media',
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/media_folder.png'
);

// @todo remove me as of TYPO3 6.2 because sys_file is categorized by default.
// @todo open issue on to make category 'l10n_mode' => 'exclude' forge.typo3.org/projects/typo3v4-core/issues

// Remove edit wizard because it's not working with the TCA tree
$options['fieldConfiguration']['wizards']['edit'] = '__UNSET';
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable('media', 'sys_file', 'categories', $options);

?>