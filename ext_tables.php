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

	/** @var \TYPO3\CMS\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Module\ModuleLoader', 'sys_file');
	$moduleLoader->setIcon('EXT:media/ext_icon.gif')
		->setModuleLanguageFile('LLL:EXT:media/Resources/Private/Language/locallang.xlf')
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
			array('TYPO3\CMS\Media\View\Menu\StorageMenu')
		)
		->setDocHeaderBottomLeftComponents(
			array('TYPO3\CMS\Media\View\Button\UploadButton')
		)
		->setGridTopComponents(
			array('TYPO3\CMS\Media\View\Check\ConfigurationCheck')
		)
		->setGridBottomComponents(
			array(
				'TYPO3\CMS\Media\View\Plugin\LinkCreatorPlugin',
				'TYPO3\CMS\Media\View\Plugin\ImageEditorPlugin',
				'TYPO3\CMS\Media\View\Plugin\FilePickerPlugin',
			)
		)
		->setGridButtonsComponents(
			array(
				'TYPO3\CMS\Media\View\Button\LinkCreatorButton',
				'TYPO3\CMS\Media\View\Button\ImageEditorButton',
				'TYPO3\CMS\Media\View\Button\FilePickerButton',
				'TYPO3\CMS\Media\View\Button\EditButton',
				'TYPO3\CMS\Media\View\Button\DownloadButton',
				'TYPO3\CMS\Media\View\Button\DeleteButton',
			)
		)
		->setMenuMassActionComponents(
			array(
				'TYPO3\CMS\Vidi\View\MenuItem\ExportXlsMenuItem',
				'TYPO3\CMS\Vidi\View\MenuItem\ExportXmlMenuItem',
				'TYPO3\CMS\Vidi\View\MenuItem\ExportCsvMenuItem',
				'TYPO3\CMS\Vidi\View\MenuItem\DividerMenuItem',

				// Media custom View Helper
				'TYPO3\CMS\Media\View\MenuItem\FilePickerMenuItem',
				'TYPO3\CMS\Media\View\MenuItem\ChangeStorageMenuItem',
				'TYPO3\CMS\Vidi\View\MenuItem\MassDeleteMenuItem',
			)
		)
		->register();

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

	/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
	$signalSlotDispatcher = $objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

	# Register some tool for Media
	\TYPO3\CMS\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'TYPO3\CMS\Media\Tool\ThumbnailGeneratorTool');
	\TYPO3\CMS\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'TYPO3\CMS\Media\Tool\CacheWarmUpTool');
	\TYPO3\CMS\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'TYPO3\CMS\Media\Tool\MissingFilesFinderTool');
	\TYPO3\CMS\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'TYPO3\CMS\Media\Tool\DuplicateRecordsFinderTool');
	\TYPO3\CMS\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'TYPO3\CMS\Media\Tool\DuplicateFilesFinderTool');

	// Connect some signals with slots
	$signalSlotDispatcher->connect(
		'TYPO3\CMS\Vidi\Controller\Backend\ContentController',
		'postProcessMatcherObject',
		'TYPO3\CMS\Media\Security\FilePermissionsAspect',
		'addFilePermissionsForFileStorages',
		TRUE
	);
	$signalSlotDispatcher->connect(
		'TYPO3\CMS\Vidi\Domain\Repository\ContentRepository',
		'postProcessComputedConstraintsObject',
		'TYPO3\CMS\Media\Security\FilePermissionsAspect',
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
