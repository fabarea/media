<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register basic metadata extractor. Will feed the file with a "title" when indexing, e.g. upload, through scheduler
\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('Fab\Media\Index\TitleMetadataExtractor');

// Hook for traditional file upload, trigger metadata indexing as well.
// Could be done at the Core level in the future...
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData'][] = 'Fab\Media\Hook\FileUploadHook';

if (TYPO3_MODE == 'BE') {

	// Special process to fill column "usage" which indicates the total number of file reference including soft references.
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Fab\Media\Hook\DataHandlerHook';

	# Configuration for RTE
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator'] = array();
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['objectReference'] = 'EXT:media/Resources/Private/HtmlArea/LinkCreator/class.tx_rtehtmlarea_linkcreator.php:&tx_rtehtmlarea_linkcreator';
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['addIconsToSkin'] = 1;
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['disableInFE'] = 1;

	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor'] = array();
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['objectReference'] = 'EXT:media/Resources/Private/HtmlArea/ImageEditor/class.tx_rtehtmlarea_imageeditor.php:&tx_rtehtmlarea_imageeditor';
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['addIconsToSkin'] = 1;
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['disableInFE'] = 1;

	// Setting up scripts that can be run from the cli_dispatch.phpsh script.
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Fab\Media\Command\FileCacheCommandController';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Fab\Media\Command\MissingFilesCommandController';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Fab\Media\Command\DuplicateFilesCommandController';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Fab\Media\Command\DuplicateRecordsCommandController';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Fab\Media\Command\ThumbnailCommandController';

	$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['media']);
	$hasMediaFilePicker = isset($configuration['has_media_file_picker']) ? $configuration['has_media_file_picker'] : FALSE;
	if ($hasMediaFilePicker) {

		// Override classes for the Object Manager.
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Backend\Form\FormEngine'] = array(
			'className' => 'Fab\Media\Override\Backend\Form\FormEngine'
		);
	}

}