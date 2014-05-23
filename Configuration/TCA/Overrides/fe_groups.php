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
				// @todo
				'MM' => 'sys_file_fegroups_mm',
				'MM_opposite_field' => 'fe_groups',
			),
		),
	),
);
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_groups'], $tca);