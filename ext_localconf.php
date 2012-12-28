<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add mapping of Media domain model to tables
	// Notice: this should have not effect since Media is not using the Extbase persistence
$tableMapping = '
config.tx_extbase.persistence.classes {
	Tx_Dam_Domain_Model_Media.mapping.tableName = sys_file
	Tx_Dam_Domain_Model_Text.mapping.tableName = sys_file
	Tx_Dam_Domain_Model_Image.mapping.tableName = sys_file
	Tx_Dam_Domain_Model_Audio.mapping.tableName = sys_file
	Tx_Dam_Domain_Model_Video.mapping.tableName = sys_file
	Tx_Dam_Domain_Model_Software.mapping.tableName = sys_file
}
';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('media', 'setup', $tableMapping);

	// register special TCE tx_media processing
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:media/Classes/Hooks/TCE.php:&Tx_Media_Hooks_TCE';

// Override classes for the Object Manager
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Core\Resource\ResourceStorage'] =
	array('className' => 'TYPO3\CMS\Media\Override\Core\Resource\ResourceStorage');

?>
