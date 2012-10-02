<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add mapping of Media domain model to tables
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
t3lib_extMgm::addTypoScript('media', 'setup', $tableMapping);

	// register special TCE tx_media processing
#$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:media/Classes/Hooks/TCE.php:&Tx_Media_Hooks_TCE';

?>
