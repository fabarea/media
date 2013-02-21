<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// register special TCE tx_media processing
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:media/Classes/Hooks/TCE.php:&Tx_Media_Hooks_TCE';

// Override classes for the Object Manager
// @todo can be removed I guess since used of add() method of FAL (and not addUploaded - or something like that)
#$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Core\Resource\ResourceStorage'] =
#	array('className' => 'TYPO3\CMS\Media\Override\Core\Resource\ResourceStorage');

?>
