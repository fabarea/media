<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA["sys_file_variants"] = array (
	"ctrl" => $TCA["sys_file_variants"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "role,original,variant"
	),
	"columns" => array (
		"role" => array (
			"exclude" => 0,
			"label" => "LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role",
			"config" => array (
				"type" => "select",
				"items" => array (
					array("LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.none", 0),
					array("LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.thumbnail", 1),
					array("LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.subtitle", 2),
					array("LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.caption", 3),
					array("LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.role.alternative", 4),
				),
				'default' => 0,
				"size" => 1,
				'minitems' => 1,
				"maxitems" => 1
			)
		),
		"original" => array (
			"exclude" => 0,
			"label" => "LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.original",
			"config" => array (
				"type" => "select",
				"foreign_table" => "sys_file",
				"size" => 1,
				"minitems" => 1,
				"maxitems" => 1,
			)
		),
		"variant" => array (
			"exclude" => 0,
			"label" => "LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file_variant.variant",
			"config" => array (
				"type" => "group",
				'internal_type' => 'db',
				"allowed" => "sys_file",
				"size" => 1,
				"minitems" => 1,
				"maxitems" => 1,
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "original,--palette--;;1"),
	),
	"palettes" => array (
		'1' => array("showitem" => "role,variant", "canNotCollapse" => 1),
	)
);
?>
