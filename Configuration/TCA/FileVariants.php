<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA["sys_file_variants"] = array (
	"ctrl" => $TCA["sys_file_variants"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "role,original,variant"
	),
	"columns" => array (
		"role" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.role",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.role.none", 0),
					Array("LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.role.alternative", 1),
					Array("LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.role.subtitle", 2),
					Array("LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.role.caption", 3),
					Array("LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.role.thumbnail", 4),
				),
				'default' => 0,
				"size" => 1,
				'minitems' => 1,
				"maxitems" => 1
			)
		),
		"original" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.original",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "sys_file",
				"size" => 1,
				"minitems" => 1,
				"maxitems" => 1,
			)
		),
		"variant" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:media/Resources/Private/Language/locallang_db.xml:sys_file_variant.variant",
			"config" => Array (
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