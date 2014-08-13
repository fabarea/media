<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

	/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
	$configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
	$configuration = $configurationUtility->getCurrentConfiguration($_EXTKEY);

	// Default User TSConfig to be added in any case.
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('

		# Enable or disabled the Media File Picker in the BE
		options.vidi.enableMediaFilePicker = 1

		# Hide the module in the BE.
		options.hideModules.user := addToList(MediaM1)

	');

	// Possibly load additional User TSConfig.
	if ($configuration['load_rte_configuration']['value'] == 1) {

		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
			RTE.default.showButtons := addToList(linkcreator,imageeditor)

			// Toolbar order
			// Must be completely reset
			RTE.default.toolbarOrder = formatblock, blockstyle, textstyle, linebreak, bold, italic, underline, strikethrough, bar, textcolor, bgcolor, bar, orderedlist, unorderedlist, bar, left, center, right, justifyfull, copy, cut, paste, bar, undo, redo, bar, findreplace, removeformat, bar, link, unlink, linkcreator, bar, imageeditor, bar, table, bar, line, bar, insertparagraphbefore, insertparagraphafter, bar, chMode, showhelp, about, linebreak, tableproperties, rowproperties, rowinsertabove, rowinsertunder, rowdelete, rowsplit, columninsertbefore, columninsertafter, columndelete, columnsplit, cellproperties, cellinsertbefore, cellinsertafter, celldelete, cellsplit, cellmerge

			RTE.default.RTEHeightOverride = 700
			RTE.default.RTEWidthOverride = 700
		');
	}

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		$_EXTKEY,
		'user', // Make media module a submodule of 'user'
		'm1',
		'bottom', // Position
		array(
			'Tool' => 'welcome, analyseIndex, deleteMissingFiles',
			'Asset' => 'show, create, update, move , delete, massDelete',
			'ImageEditor' => 'show',
			'Storage' => 'list',
			'LinkCreator' => 'show',
			'ProcessedFile' => 'create',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:media/ext_icon.gif',
			'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
		)
	);

	/** @var \TYPO3\CMS\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Module\ModuleLoader', 'sys_file');
	$moduleLoader->setIcon('EXT:media/ext_icon.gif')
		->setModuleLanguageFile('LLL:EXT:media/Resources/Private/Language/locallang.xlf')
		->addJavaScriptFiles(
			array(
				'EXT:media/Resources/Public/JavaScript/JQuery/jquery.fineuploader-3.4.1.js',
				'EXT:media/Resources/Public/JavaScript/Initialize.js',
				'EXT:media/Resources/Public/JavaScript/Media.js',
			)
		)
		->addStyleSheetFiles(
			array(
				'EXT:media/Resources/Public/StyleSheets/media.css',
				'EXT:media/Resources/Public/StyleSheets/FileUploader/fineuploader.css'
			)
		)
		->setDocHeaderTopLeftComponents(
			array('TYPO3\CMS\Media\ViewHelpers\Component\MenuStorageViewHelper')
		)
		->setDocHeaderTopRightComponents(
			array('TYPO3\CMS\Media\ViewHelpers\Component\ButtonToolViewHelper')
		)
		->setDocHeaderBottomLeftComponents(
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
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonEditViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\ButtonDeleteViewHelper',
			)
		)
		->setMenuSelectedRowsComponents(
			array(
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemExportXlsViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemExportXmlViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemExportCsvViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemDividerViewHelper',

				// Media custom View Helper
				'TYPO3\CMS\Media\ViewHelpers\Component\MenuItemFilePickerViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\MenuItemChangeStorageViewHelper',
				'TYPO3\CMS\Media\ViewHelpers\Component\MenuItemMassDeleteViewHelper',
			)
		)
		->setMenuAllRowsComponents(
			array(
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemExportXlsViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemExportXmlViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemExportCsvViewHelper',
				'TYPO3\CMS\Vidi\ViewHelpers\Component\MenuItemDividerViewHelper',

				// Media custom View Helper
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
		'TYPO3\CMS\Media\Security\FilePermissionsAspect',
		'addFilePermissions',
		TRUE
	);

	// Connect "afterFindContentObject" signal slot with the "ContentObjectProcessor".
	$signalSlotDispatcher->connect(
		'TYPO3\CMS\Vidi\Controller\Backend\ContentController',
		'afterFindContentObjects',
		'TYPO3\CMS\Media\Filter\UsageFilter',
		'filter',
		TRUE
	);
}

\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(
	array(
		'image-edit' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_edit.png',
		'image-link' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_link.png',
		'image-export' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/image_export.png',
		'storage-change' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/folder_go.png',
	),
	'media'
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
