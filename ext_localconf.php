<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use Fab\Media\Backend\TceForms;
use TYPO3\CMS\Core\Resource\Index\ExtractorRegistry;
use Fab\Media\Index\TitleMetadataExtractor;
use TYPO3\CMS\Backend\Form\FormResultCompiler;

defined('TYPO3') or die();

call_user_func(function () {
    $configuration = GeneralUtility::makeInstance(
        ExtensionConfiguration::class
    )->get('media');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1547054767] = [
        'nodeName' => 'findUploader',
        'priority' => 40,
        'class' => TceForms::class,
    ];

    $disableTitleMetadataExtractor = isset($configuration['disable_title_metadata_extractor']) ? $configuration['disable_title_metadata_extractor'] : false;
    if (!$disableTitleMetadataExtractor) {
        // Register basic metadata extractor. Will feed the file with a "title" when indexing, e.g. upload, through scheduler
        GeneralUtility::makeInstance(ExtractorRegistry::class)->registerExtractionService(TitleMetadataExtractor::class);
    }

    // Hook for traditional file upload, trigger metadata indexing as well.
    // Could be done at the Core level in the future...
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData'][] = 'Fab\Media\Hook\FileUploadHook';


    // Special process to fill column "usage" which indicates the total number of file reference including soft references.
    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Fab\Media\Hook\DataHandlerHook';

    $hasMediaFilePicker = isset($configuration['has_media_file_picker']) ? $configuration['has_media_file_picker'] : false;
    if ($hasMediaFilePicker) {
        // Override classes for the Object Manager.
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][FormResultCompiler::class] = array(
            'className' => \Fab\Media\Override\Backend\Form\FormResultCompiler::class
        );
    }
});
