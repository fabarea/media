<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'columns' => array(
		'files' => array(
			'config' => array(
				'type' => 'select',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 9999,
				'autoSizeMax' => 30,
				'multiple' => 0,
				'foreign_table' => 'sys_file',
				'MM' => 'sys_file_begroups_mm',
				'MM_opposite_field' => 'be_groups',
			),
		),
	),
);

return \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($GLOBALS['TCA']['be_groups'], $tca);
?>