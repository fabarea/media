<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

# Hook for secure download in Frontend
# Hook is not enabled by default for now and must be commented out. More info in Documentation.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/naw_securedl/class.tx_nawsecuredl_output.php']['preOutput'][] = 'EXT:media/Classes/Hooks/NawSecuredl.php:TYPO3\CMS\Media\Hooks\NawSecuredl->preOutput';

if (TYPO3_MODE == 'BE') {

	# Configuration for RTE
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator'] = array();
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['objectReference'] = 'EXT:' . $_EXTKEY . '/Resources/Private/HtmlArea/LinkCreator/class.tx_rtehtmlarea_linkcreator.php:&tx_rtehtmlarea_linkcreator';
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['addIconsToSkin'] = 1;
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['LinkCreator']['disableInFE'] = 1;

	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor'] = array();
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['objectReference'] = 'EXT:' . $_EXTKEY . '/Resources/Private/HtmlArea/ImageEditor/class.tx_rtehtmlarea_imageeditor.php:&tx_rtehtmlarea_imageeditor';
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['addIconsToSkin'] = 1;
	$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['ImageEditor']['disableInFE'] = 1;

	// Setting up scripts that can be run from the cli_dispatch.phpsh script.
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'TYPO3\CMS\Media\Command\MediaCommandController';
}
?>