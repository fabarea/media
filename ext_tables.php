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
        'Fab.media',
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

    $defaultMainModule = (bool)$configuration['has_folder_tree']['value'] ? 'file' : 'content';

    /** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
    $moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader', 'sys_file');
    $moduleLoader->setIcon('EXT:media/ext_icon.gif')
        ->setModuleLanguageFile($moduleFileLanguage)
        ->setMainModule($defaultMainModule)
        ->addJavaScriptFiles(
            array(
                'EXT:media/Resources/Public/JavaScript/Initialize.js',
                'EXT:media/Resources/Public/JavaScript/Media.BrowseRecursively.js',
                'EXT:media/Resources/Public/Libraries/Fineuploader/jquery.fineuploader-5.0.9.min.js',
            )
        )
        ->addStyleSheetFiles(
            array(
                'EXT:media/Resources/Public/StyleSheets/media.css',
                'EXT:media/Resources/Public/StyleSheets/fineuploader.css',
            )
        )
        ->setDocHeaderTopLeftComponents(
            array(
                'Fab\Media\View\Menu\StorageMenu',
                'Fab\Media\View\Checkbox\RecursiveCheckbox'
            )
        )
        ->setDocHeaderBottomLeftComponents(
            array(
                'Fab\Vidi\View\Button\ClipboardButton',
                'Fab\Media\View\Button\NewFolder',
                'Fab\Media\View\Button\UploadButton',
            )
        )
        ->setGridTopComponents(
            array(
                'Fab\Media\View\InlineJavaScript',
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
                'Fab\Vidi\View\MenuItem\ClipboardMenuItem',
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

    # Register some tool for Media.
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\ThumbnailGeneratorTool');
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\CacheWarmUpTool');
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\MissingFilesFinderTool');
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\DuplicateRecordsFinderTool');
    \Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\DuplicateFilesFinderTool');

    // Connect some signals with slots.
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

    $signalSlotDispatcher->connect(
        'Fab\Vidi\Service\ContentService',
        'afterFindContentObjects',
        'Fab\Media\Facet\ActionPermissionFacet',
        'modifyResultSet',
        TRUE
    );

    // @bug Class property $relativePathToSkin does not look to be working anymore since TYPO3 7. Workaround: load CSS for the RTE as skin.
    // Reference EXT:media/Classes/Rtehtmlarea/Extension/LinkCreator.php
    if (is_file(PATH_site . 'typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins/LinkCreator.js') ||
        is_file(PATH_site . 'typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins/ImageEditor.js')) {

        // Register as a skin
        $GLOBALS['TBE_STYLES']['skins']['media'] = array(
            'name' => 'media',
            'stylesheetDirectories' => array(
                'css' => 'EXT:media/Resources/Public/HtmlArea/'
            )
        );
    }
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
