<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

	/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
	$configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
	$configuration = $configurationUtility->getCurrentConfiguration('media');

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
		'media',
		'user', // Make media module a submodule of 'user'
		'm1',
		'bottom', // Position
		array(
			'Asset' => 'create, update, download, editStorage',
			'ImageEditor' => 'show',
			'LinkCreator' => 'show',
			'ProcessedFile' => 'create',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:media/ext_icon.gif',
			'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
		)
	);

	$defaultMainModule = 'file';
	if ((bool)$configuration['hide_folder_tree']['value']) {
		$defaultMainModule = 'user';
	}

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader', 'sys_file');
	$moduleLoader->setIcon('EXT:media/ext_icon.gif')
		->setModuleLanguageFile('LLL:EXT:media/Resources/Private/Language/locallang.xlf')
		->setMainModule($defaultMainModule)
		->addJavaScriptFiles(
			array(
				'EXT:media/Resources/Public/Build/media.min.js',
			)
		)
		->addStyleSheetFiles(
			array(
				'EXT:media/Resources/Public/Build/media.min.css',
			)
		)
		->setDocHeaderTopLeftComponents(
			array(
				'Fab\Media\View\Menu\StorageMenu',
				'Fab\Media\View\Checkbox\RecursiveCheckbox'
			)
		)
		->setDocHeaderBottomLeftComponents(
			array('Fab\Media\View\Button\UploadButton')
		)
		->setGridTopComponents(
			array(
				'Fab\Media\View\Warning\ConfigurationWarning',
				'Fab\Media\View\Info\SelectedFolderInfo',
			)
		)
		->setGridBottomComponents(
			array(
				'Fab\Media\View\Plugin\LinkCreatorPlugin',
				'Fab\Media\View\Plugin\ImageEditorPlugin',
				'Fab\Media\View\Plugin\FilePickerPlugin',
			)
		)
		->setGridButtonsComponents(
			array(
				'Fab\Media\View\Button\LinkCreatorButton',
				'Fab\Media\View\Button\ImageEditorButton',
				'Fab\Media\View\Button\FilePickerButton',
				'Fab\Media\View\Button\EditButton',
				'Fab\Media\View\Button\DownloadButton',
				'Fab\Media\View\Button\DeleteButton',
			)
		)
		->setMenuMassActionComponents(
			array(
				'Fab\Vidi\View\MenuItem\ExportXlsMenuItem',
				'Fab\Vidi\View\MenuItem\ExportXmlMenuItem',
				'Fab\Vidi\View\MenuItem\ExportCsvMenuItem',
				'Fab\Vidi\View\MenuItem\DividerMenuItem',

				// Media custom View Helper
				'Fab\Media\View\MenuItem\FilePickerMenuItem',
				'Fab\Media\View\MenuItem\ChangeStorageMenuItem',
				'Fab\Vidi\View\MenuItem\MassDeleteMenuItem',
			)
		)
		->register();

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

	/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
	$signalSlotDispatcher = $objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

	# Register some tool for Media
	\Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\ThumbnailGeneratorTool');
	\Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\CacheWarmUpTool');
	\Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\MissingFilesFinderTool');
	\Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\DuplicateRecordsFinderTool');
	\Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\DuplicateFilesFinderTool');

	// Connect some signals with slots
	$signalSlotDispatcher->connect(
		'Fab\Vidi\Controller\Backend\ContentController',
		'postProcessMatcherObject',
		'Fab\Media\Security\FilePermissionsAspect',
		'addFilePermissionsForFileStorages',
		TRUE
	);
	$signalSlotDispatcher->connect(
		'Fab\Vidi\Domain\Repository\ContentRepository',
		'postProcessConstraintsObject',
		'Fab\Media\Security\FilePermissionsAspect',
		'addFilePermissionsForFileMounts',
		TRUE
	);

}

\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(
	array(
		'image-edit' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/image_edit.png',
		'image-link' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/image_link.png',
		'image-export' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/image_export.png',
		'storage-change' => TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/folder_go.png',
	),
	'media'
);
