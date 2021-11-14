<?php
defined('TYPO3') or die();

call_user_func(function () {

    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('media');

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1547054767] = [
        'nodeName' => 'findUploader',
        'priority' => 40,
        'class' => \Fab\Media\Backend\TceForms::class,
    ];

    $disableTitleMetadataExtractor = isset($configuration['disable_title_metadata_extractor']) ? $configuration['disable_title_metadata_extractor'] : FALSE;
    if (!$disableTitleMetadataExtractor) {

        // Register basic metadata extractor. Will feed the file with a "title" when indexing, e.g. upload, through scheduler
        \TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService(\Fab\Media\Index\TitleMetadataExtractor::class);
    }

    // Hook for traditional file upload, trigger metadata indexing as well.
    // Could be done at the Core level in the future...
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData'][] = 'Fab\Media\Hook\FileUploadHook';


    // Special process to fill column "usage" which indicates the total number of file reference including soft references.
    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Fab\Media\Hook\DataHandlerHook';

    $hasMediaFilePicker = isset($configuration['has_media_file_picker']) ? $configuration['has_media_file_picker'] : FALSE;
    if ($hasMediaFilePicker) {

        // Override classes for the Object Manager.
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\FormResultCompiler::class] = array(
            'className' => \Fab\Media\Override\Backend\Form\FormResultCompiler::class
        );
    }
});