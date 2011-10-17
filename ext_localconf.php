<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add mapping of Media domain model to tables
$tableMapping = '
config.tx_extbase.persistence.classes {
	Tx_Dam_Domain_Model_Media.mapping.tableName = tx_media
	Tx_Dam_Domain_Model_Text.mapping.tableName = tx_media
	Tx_Dam_Domain_Model_Image.mapping.tableName = tx_media
	Tx_Dam_Domain_Model_Audio.mapping.tableName = tx_media
	Tx_Dam_Domain_Model_Video.mapping.tableName = tx_media
	Tx_Dam_Domain_Model_Software.mapping.tableName = tx_media
}
';
t3lib_extMgm::addTypoScript('media', 'setup', $tableMapping);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Frontend',
	array(
		'Media' => 'show, list, new, create, edit, update, delete',
		'Collection' => 'list, show, new, create, edit, update, delete',
		'Filter' => 'list, show, new, create, edit, update, delete',
		'File' => 'show, list, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Media' => 'create, update, delete',
		'Collection' => 'create, update, delete',
		'Filter' => 'create, update, delete',
		'File' => 'create, update, delete',
		
	)
);

	// register special TCE tx_media processing
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:media/Classes/Hooks/TCE.php:&Tx_Media_Hooks_TCE';

?>
