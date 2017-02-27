<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
    $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

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

    $moduleFileLanguage = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf';
    if ($configuration['hide_file_list']['value'] == 1) {

        $moduleFileLanguage = 'LLL:EXT:media/Resources/Private/Language/locallang_filelist.xlf';

        // Default User TSConfig to be added in any case.
        TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('

			# Hide default File List
			options.hideModules.file := addToList(FilelistList)
		');
    }

    // Possibly load additional User TSConfig.
    if ((int)$configuration['load_rte_configuration']['value'] === 1) {

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
        'Fab.media',
        'user', // Make media module a submodule of 'user'
        'm1',
        'bottom', // Position
        [
            'Asset' => 'create, update, download, editStorage',
            'ImageEditor' => 'show',
            'LinkCreator' => 'show',
            'ProcessedFile' => 'create',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:media/ext_icon.svg',
            'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
        ]
    );

    $defaultMainModule = (bool)$configuration['has_folder_tree']['value'] ? 'file' : 'content';

    /** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
    $moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Fab\Vidi\Module\ModuleLoader::class,
        'sys_file'
    );
    $moduleLoader->setIcon('EXT:media/ext_icon.svg')
        ->setModuleLanguageFile($moduleFileLanguage)
        ->setMainModule($defaultMainModule)
        ->addJavaScriptFiles([
            'EXT:media/Resources/Public/Libraries/Fineuploader/jquery.fineuploader-5.0.9.min.js',
        ])
        ->addStyleSheetFiles([
            'EXT:media/Resources/Public/StyleSheets/media.css',
            'EXT:media/Resources/Public/StyleSheets/fineuploader.css',
        ])
        ->setDocHeaderTopLeftComponents([
                \Fab\Media\View\Menu\StorageMenu::class,
                \Fab\Media\View\Checkbox\RecursiveCheckbox::class,]
        )
        ->setDocHeaderBottomLeftComponents([
            \Fab\Vidi\View\Button\ClipboardButton::class,
            \Fab\Media\View\Button\NewFolder::class,
            \Fab\Media\View\Button\UploadButton::class,
        ])
        ->setGridTopComponents([
            \Fab\Media\View\InlineJavaScript::class,
            \Fab\Media\View\Warning\ConfigurationWarning::class,
            \Fab\Media\View\Info\SelectedFolderInfo::class,
        ])
        ->setGridBottomComponents([
            \Fab\Media\View\Plugin\LinkCreatorPlugin::class,
            \Fab\Media\View\Plugin\ImageEditorPlugin::class,
            \Fab\Media\View\Plugin\FilePickerPlugin::class,
        ])
        ->setGridButtonsComponents([
            \Fab\Media\View\Button\LinkCreatorButton::class,
            \Fab\Media\View\Button\ImageEditorButton::class,
            \Fab\Media\View\Button\FilePickerButton::class,
            \Fab\Media\View\Button\EditButton::class,
            \Fab\Media\View\Button\DownloadButton::class,
            \Fab\Media\View\Button\DeleteButton::class,
        ])
        ->setMenuMassActionComponents([
            \Fab\Vidi\View\MenuItem\ExportXlsMenuItem::class,
            \Fab\Vidi\View\MenuItem\ExportXmlMenuItem::class,
            \Fab\Vidi\View\MenuItem\ExportCsvMenuItem::class,
            \Fab\Vidi\View\MenuItem\DividerMenuItem::class,

            // Media custom View Helper
            \Fab\Vidi\View\MenuItem\ClipboardMenuItem::class,
            \Fab\Media\View\MenuItem\FilePickerMenuItem::class,
            \Fab\Media\View\MenuItem\ChangeStorageMenuItem::class,
            \Fab\Vidi\View\MenuItem\MassDeleteMenuItem::class,
        ])
        ->register();

    /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
    $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

    /** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
    $signalSlotDispatcher = $objectManager->get(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

    # Register some tool for Media.
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', \Fab\Media\Tool\ThumbnailGeneratorTool::class);
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', \Fab\Media\Tool\CacheWarmUpTool::class);
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', \Fab\Media\Tool\MissingFilesFinderTool::class);
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', \Fab\Media\Tool\DuplicateRecordsFinderTool::class);
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', \Fab\Media\Tool\DuplicateFilesFinderTool::class);

    // Connect some signals with slots.
    $signalSlotDispatcher->connect(
        'Fab\Vidi\Controller\Backend\ContentController', // Small exception in naming here as the class was previously located in "Controller\Backend".
        'postProcessMatcherObject',
        \Fab\Media\Security\FilePermissionsAspect::class,
        'addFilePermissionsForFileStorages'
    );

    $signalSlotDispatcher->connect(
        \Fab\Vidi\Domain\Repository\ContentRepository::class,
        'postProcessConstraintsObject',
        \Fab\Media\Security\FilePermissionsAspect::class,
        'addFilePermissionsForFileMounts'
    );

    $signalSlotDispatcher->connect(
        \Fab\Vidi\Service\ContentService::class,
        'afterFindContentObjects',
        \Fab\Media\Facet\ActionPermissionFacet::class,
        'modifyResultSet'
    );

    // @bug Class property $relativePathToSkin does not look to be working anymore since TYPO3 7. Workaround: load CSS for the RTE as skin.
    // Reference EXT:media/Classes/Rtehtmlarea/Extension/LinkCreator.php
    if (is_file(PATH_site . 'typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins/LinkCreator.js') ||
        is_file(PATH_site . 'typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins/ImageEditor.js')
    ) {

        // Register as a skin
        $GLOBALS['TBE_STYLES']['skins']['media'] = [
            'name' => 'media',
            'stylesheetDirectories' => [
                'css' => 'EXT:media/Resources/Public/HtmlArea/'
            ]
        ];
    }
}

// Add new sprite icon.
$icons = [
    'image-edit' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/image_edit.png',
    'image-link' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/image_link.png',
    'image-export' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/image_export.png',
    'storage-change' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/folder_go.png',
];
/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
foreach ($icons as $key => $icon) {
    $iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-' . $key,
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        [
            'source' => $icon
        ]
    );
}
unset($iconRegistry);
