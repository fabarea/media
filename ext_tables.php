<?php
defined('TYPO3') or die();

call_user_func(function () {


    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('media');

    // Default User TSConfig to be added in any case.
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('

		# Enable or disabled the Media File Picker in the BE
		options.vidi.enableMediaFilePicker = 1

		# Hide the module in the BE.
		options.hideModules.user := addToList(MediaM1)

	');

    $moduleFileLanguage = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf';
    if ($configuration['hide_file_list'] == 1) {

        $moduleFileLanguage = 'LLL:EXT:media/Resources/Private/Language/locallang_filelist.xlf';

        // Default User TSConfig to be added in any case.
        TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('

			# Hide default File List
			options.hideModules.file := addToList(FilelistList)
		');
    }

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Fab.media',
        'user', // Make media module a submodule of 'user'
        'm1',
        'bottom', // Position
        [
            \Fab\Media\Controller\AssetController::class => 'create, update, download, editStorage',
            \Fab\Media\Controller\ImageEditorController::class => 'show',
            \Fab\Media\Controller\LinkCreatorController::class => 'show',
            \Fab\Media\Controller\ProcessedFileController::class => 'create',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:media/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
        ]
    );

    $defaultMainModule = (bool)$configuration['has_folder_tree'] ? 'file' : 'content';

    /** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
    $moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Fab\Vidi\Module\ModuleLoader::class,
        'sys_file'
    );
    $moduleLoader->setIcon('EXT:media/Resources/Public/Icons/Extension.svg')
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

    // Add new sprite icon.
    $icons = [
        'image-edit' => 'EXT:media/Resources/Public/Icons/image_edit.png',
        'image-link' => 'EXT:media/Resources/Public/Icons/image_link.png',
        'image-export' => 'EXT:media/Resources/Public/Icons/image_export.png',
        'storage-change' => 'EXT:media/Resources/Public/Icons/folder_go.png',
    ];
    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    foreach ($icons as $key => $icon) {
        $iconRegistry->registerIcon('extensions-media-' . $key,
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            [
                'source' => $icon
            ]
        );
    }
    unset($iconRegistry);

});

