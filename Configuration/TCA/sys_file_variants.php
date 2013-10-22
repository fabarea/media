<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

return array (
	'ctrl' => array(
		'title' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variants',
		'label' => 'role',
		'label_alt' => 'original',
		'label_alt_force' => 'true',
		'hideTable' => TRUE,
		'security' => array(
			'ignoreRootLevelRestriction' => 1,
			'ignoreWebMountRestriction' => 1,
		),
		'rootLevel' => 1,
		'typeicon_classes' => array(
			'default' => 'extensions-media-variant',
		),
	),
	'interface' => array (
		'showRecordFieldList' => 'role,original,variant'
	),
	'columns' => array (
		'role' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role',
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.none', 0),
					array('LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.thumbnail', 1),
					array('LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.subtitle', 2),
					array('LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.caption', 3),
					array('LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.alternative', 4),
				),
				'default' => 0,
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1
			)
		),
		'original' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.original',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_file',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'variant' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.variant',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'sys_file',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'original,--palette--;;1'),
	),
	'palettes' => array (
		'1' => array('showitem' => 'role,variant', 'canNotCollapse' => 1),
	)
);
?>