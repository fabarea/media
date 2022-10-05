<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Fab\Media\Controller\AssetController;
use Fab\Media\Controller\ImageEditorController;
use Fab\Media\Controller\LinkCreatorController;
use Fab\Media\Controller\ProcessedFileController;
use Fab\Vidi\Module\ModuleLoader;
use Fab\Media\View\Menu\StorageMenu;
use Fab\Media\View\Checkbox\RecursiveCheckbox;
use Fab\Vidi\View\Button\ClipboardButton;
use Fab\Media\View\Button\NewFolder;
use Fab\Media\View\Button\UploadButton;
use Fab\Media\View\InlineJavaScript;
use Fab\Media\View\Warning\ConfigurationWarning;
use Fab\Media\View\Info\SelectedFolderInfo;
use Fab\Media\View\Plugin\LinkCreatorPlugin;
use Fab\Media\View\Plugin\ImageEditorPlugin;
use Fab\Media\View\Plugin\FilePickerPlugin;
use Fab\Media\View\Button\LinkCreatorButton;
use Fab\Media\View\Button\ImageEditorButton;
use Fab\Media\View\Button\FilePickerButton;
use Fab\Media\View\Button\EditButton;
use Fab\Media\View\Button\DownloadButton;
use Fab\Media\View\Button\DeleteButton;
use Fab\Vidi\View\MenuItem\ExportXlsMenuItem;
use Fab\Vidi\View\MenuItem\ExportXmlMenuItem;
use Fab\Vidi\View\MenuItem\ExportCsvMenuItem;
use Fab\Vidi\View\MenuItem\DividerMenuItem;
use Fab\Vidi\View\MenuItem\ClipboardMenuItem;
use Fab\Media\View\MenuItem\FilePickerMenuItem;
use Fab\Media\View\MenuItem\ChangeStorageMenuItem;
use Fab\Vidi\View\MenuItem\MassDeleteMenuItem;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use Fab\Vidi\Tool\ToolRegistry;
use Fab\Media\Tool\ThumbnailGeneratorTool;
use Fab\Media\Tool\CacheWarmUpTool;
use Fab\Media\Tool\MissingFilesFinderTool;
use Fab\Media\Tool\DuplicateRecordsFinderTool;
use Fab\Media\Tool\DuplicateFilesFinderTool;
use Fab\Media\Security\FilePermissionsAspect;
use Fab\Vidi\Domain\Repository\ContentRepository;
use Fab\Vidi\Service\ContentService;
use Fab\Media\Facet\ActionPermissionFacet;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

call_user_func(function () {
    $configuration = GeneralUtility::makeInstance(
        ExtensionConfiguration::class
    )->get('media');

    // Default User TSConfig to be added in any case.
    ExtensionManagementUtility::addUserTSConfig('

		# Enable or disabled the Media File Picker in the BE
		options.vidi.enableMediaFilePicker = 1

		# Hide the module in the BE.
		options.hideModules.user := addToList(MediaM1)

	');

    $moduleFileLanguage = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf';
    if ($configuration['hide_file_list'] == 1) {
        $moduleFileLanguage = 'LLL:EXT:media/Resources/Private/Language/locallang_filelist.xlf';

        // Default User TSConfig to be added in any case.
        ExtensionManagementUtility::addUserTSConfig('

			# Hide default File List
			options.hideModules.file := addToList(FilelistList)
		');
    }

    ExtensionUtility::registerModule(
        'Media',
        'user', // Make media module a submodule of 'user'
        'm1',
        'bottom', // Position
        [
            AssetController::class => 'create, update, download, editStorage',
            ImageEditorController::class => 'show',
            LinkCreatorController::class => 'show',
            ProcessedFileController::class => 'create',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:media/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:media/Resources/Private/Language/locallang_module.xlf',
        ]
    );

    $defaultMainModule = (bool)$configuration['has_folder_tree'] ? 'file' : 'content';

    /** @var ModuleLoader $moduleLoader */
    $moduleLoader = GeneralUtility::makeInstance(
        ModuleLoader::class,
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
        ->setDocHeaderTopLeftComponents(
            [
                StorageMenu::class,
                RecursiveCheckbox::class,]
        )
        ->setDocHeaderBottomLeftComponents([
            ClipboardButton::class,
            NewFolder::class,
            UploadButton::class,
        ])
        ->setGridTopComponents([
            InlineJavaScript::class,
            ConfigurationWarning::class,
            SelectedFolderInfo::class,
        ])
        ->setGridBottomComponents([
            LinkCreatorPlugin::class,
            ImageEditorPlugin::class,
            FilePickerPlugin::class,
        ])
        ->setGridButtonsComponents([
            LinkCreatorButton::class,
            ImageEditorButton::class,
            FilePickerButton::class,
            EditButton::class,
            DownloadButton::class,
            DeleteButton::class,
        ])
        ->setMenuMassActionComponents([
            ExportXlsMenuItem::class,
            ExportXmlMenuItem::class,
            ExportCsvMenuItem::class,
            DividerMenuItem::class,

            // Media custom View Helper
            ClipboardMenuItem::class,
            FilePickerMenuItem::class,
            ChangeStorageMenuItem::class,
            MassDeleteMenuItem::class,
        ])
        ->register();


    /** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
    $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);

    # Register some tool for Media.
    ToolRegistry::getInstance()->register('sys_file', ThumbnailGeneratorTool::class);
    ToolRegistry::getInstance()->register('sys_file', CacheWarmUpTool::class);
    ToolRegistry::getInstance()->register('sys_file', MissingFilesFinderTool::class);
    ToolRegistry::getInstance()->register('sys_file', DuplicateRecordsFinderTool::class);
    ToolRegistry::getInstance()->register('sys_file', DuplicateFilesFinderTool::class);

    // Connect some signals with slots.
    $signalSlotDispatcher->connect(
        'Fab\Vidi\Controller\Backend\ContentController', // Small exception in naming here as the class was previously located in "Controller\Backend".
        'postProcessMatcherObject',
        FilePermissionsAspect::class,
        'addFilePermissionsForFileStorages'
    );

    $signalSlotDispatcher->connect(
        ContentRepository::class,
        'postProcessConstraintsObject',
        FilePermissionsAspect::class,
        'addFilePermissionsForFileMounts'
    );

    $signalSlotDispatcher->connect(
        ContentService::class,
        'afterFindContentObjects',
        ActionPermissionFacet::class,
        'modifyResultSet'
    );

    // Add new sprite icon.
    $icons = [
        'image-edit' => 'EXT:media/Resources/Public/Icons/image_edit.png',
        'image-link' => 'EXT:media/Resources/Public/Icons/image_link.png',
        'image-export' => 'EXT:media/Resources/Public/Icons/image_export.png',
        'storage-change' => 'EXT:media/Resources/Public/Icons/folder_go.png',
    ];
    /** @var IconRegistry $iconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    foreach ($icons as $key => $icon) {
        $iconRegistry->registerIcon(
            'extensions-media-' . $key,
            BitmapIconProvider::class,
            [
                'source' => $icon
            ]
        );
    }
    unset($iconRegistry);
});
