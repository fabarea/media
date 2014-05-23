<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'ctrl' => array(
		'default_sortby' => 'ORDER BY uid DESC',
		'searchFields' => 'uid,extension,name', // sys_file_metadata.title,sys_file_metadata.keywords,
	),
	'columns' => array(
		'fileinfo' => array(
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'EXT:media/Classes/Backend/TceForms.php:TYPO3\CMS\Media\Backend\TceForms->renderFileUpload',
			),
		),
	),
);
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file_metadata'], $tca);