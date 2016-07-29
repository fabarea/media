<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Register basic metadata extractor. Will feed the file with a "title" when indexing, e.g. upload, through scheduler
\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('Fab\Media\Index\TitleMetadataExtractor');

// Hook for traditional file upload, trigger metadata indexing as well.
// Could be done at the Core level in the future...
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData'][] = 'Fab\Media\Hook\FileUploadHook';

if (TYPO3_MODE === 'BE') {

    // Special process to fill column "usage" which indicates the total number of file reference including soft references.
    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Fab\Media\Hook\DataHandlerHook';

    # Configuration for RTE
    // @bug it looks HtmlArea requires JS files to be in two locations to work for third-party plugins.
    // Still looking for a better solution.... For now I symlink files.
    // ln -s `pwd`/htdocs/typo3conf/ext/media/Resources/Public/JavaScript/Plugins/LinkCreator.js `pwd`/htdocs/typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins
    if (is_file(PATH_site . 'typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins/LinkCreator.js')) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator'] = array();
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['objectReference'] = \Fab\Media\Rtehtmlarea\Extension\LinkCreator::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['addIconsToSkin'] = 1;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['disableInFE'] = 1;
    }

    // ln -s `pwd`/htdocs/typo3conf/ext/media/Resources/Public/JavaScript/Plugins/ImageEditor.js `pwd`/htdocs/typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins
    if (is_file(PATH_site . 'typo3/sysext/rtehtmlarea/Resources/Public/JavaScript/Plugins/ImageEditor.js')) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor'] = array();
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['objectReference'] = \Fab\Media\Rtehtmlarea\Extension\ImageEditor::class;       $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['addIconsToSkin'] = 1;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['disableInFE'] = 1;
    }

    // Setting up scripts that can be run from the cli_dispatch.phpsh script.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\Media\Command\FileCacheCommandController::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\Media\Command\MissingFilesCommandController::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\Media\Command\DuplicateFilesCommandController::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\Media\Command\DuplicateRecordsCommandController::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\Media\Command\ThumbnailCommandController::class;

    $configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['media']);
    $hasMediaFilePicker = isset($configuration['has_media_file_picker']) ? $configuration['has_media_file_picker'] : FALSE;
    if ($hasMediaFilePicker) {

        // Override classes for the Object Manager.
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\FormResultCompiler::class] = array(
            'className' => \Fab\Media\Override\Backend\Form\FormResultCompiler::class
        );
    }
}