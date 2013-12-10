<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'ctrl' => array(
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'tstamp' => 'tstamp',
		'default_sortby' => 'ORDER BY is_variant ASC, uid DESC',
		'crdate' => 'crdate',
		'searchFields' => 'uid,title,keywords,extension,name',
	),
	'types' => array(
		TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => array('showitem' => '
								fileinfo, sys_language_uid,title, description, keywords, alternative, caption, download_name,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									fe_groups,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metrics;70;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

		TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array('showitem' => '
								fileinfo, sys_language_uid, title, description, keywords, alternative, caption, download_name,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									fe_groups,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

		TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array('showitem' => '
								fileinfo, sys_language_uid, title, description, keywords, alternative, caption, download_name,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									fe_groups,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metrics;70;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

		TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array('showitem' => '

								fileinfo, sys_language_uid, title, description, keywords, alternative, caption, download_name,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									fe_groups,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									duration,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metrics;70;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

		TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array('showitem' => '
								fileinfo, sys_language_uid, title, description, keywords, alternative, caption, download_name,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									fe_groups,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									duration,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

		TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array('showitem' => '
								fileinfo, sys_language_uid, title, description, keywords, alternative, caption, download_name,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									fe_groups,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),
	),
	'palettes' => array(
		'10' => array('showitem' => 'visible, status, ranking', 'canNotCollapse' => '1'),
		'30' => array('showitem' => 'publisher, source', 'canNotCollapse' => '1'),
		'50' => array('showitem' => 'latitude, longitude', 'canNotCollapse' => '1'),
		'40' => array('showitem' => 'location_country, location_region, location_city', 'canNotCollapse' => '1'),
		'60' => array('showitem' => 'creation_date, modification_date', 'canNotCollapse' => '1'),
		'70' => array('showitem' => 'width, height, unit, color_space', 'canNotCollapse' => '1'),
	),
	'columns' => array(
		'sys_language_uid' => Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => Array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array(
				'type' => 'select',
				'items' => Array(
					Array('', 0),
				),
				'foreign_table' => 'sys_file',
				'foreign_table_where' => 'AND sys_file.pid=###REC_FIELD_pid### AND sys_file.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array(
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'fileinfo' => array(
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'EXT:media/Classes/Backend/TceForms.php:TYPO3\CMS\Media\Backend\TceForms->renderFileUpload',
			),
		),
		'variants' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.variants',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'sys_file_variants',
				'foreign_selector' => 'role',
				'foreign_field' => 'original'
			)
		),
		'visible' => Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.visible',
			'config' => Array(
				'type' => 'check',
				'default' => '1'
			)
		),
		'status' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.status',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.status.1',
						1,
						\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/status_1.png'
					),
					array(
						'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.status.2',
						2,
						\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/status_2.png'
					),
					array(
						'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.status.3',
						3,
						\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/status_3.png'
					),
				),
			),
		),
		'title' => array(
			'exclude' => 0,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_file.title',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim'
			)
		),
		'description' => array(
			'exclude' => 0,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_file.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'keywords' => array(
			'exclude' => 1,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.keywords',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim'
			),
		),
		'extension' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.extension',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim'
			),
		),
		'creation_date' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.creation_date',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'modification_date' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.modification_date',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'creator_tool' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.creator_tool',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim'
			),
		),
		'download_name' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.download_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'creator' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.creator',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'publisher' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.publisher',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim'
			),
		),
		'source' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.source',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim'
			),
		),
		'alternative' => array(
			'exclude' => 1,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.alternative',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'caption' => array(
			'exclude' => 1,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.caption',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'pages' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.pages',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'note' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.note',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			),
		),
		'location_country' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => '',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.location_country',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim'
			),
		),
		'location_region' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => '',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.location_region',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim'
			),
		),
		'location_city' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'l10n_display' => '',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.location_city',
			'config' => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim'
			),
		),
		'latitude' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.latitude',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
				'max' => '30',
				'default' => '0.00000000000000'
			),
		),
		'longitude' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.longitude',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
				'max' => '30',
				'default' => '0.00000000000000'
			),
		),
		'ranking' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.ranking',
			'config' => array(
				'type' => 'select',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => array(
					array(1, 1),
					array(2, 2),
					array(3, 3),
					array(4, 4),
					array(5, 5),
				),
			),
		),
		'language' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.language',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			)
		),
		/*
		 * METRICS ###########################################
		 */
		'duration' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.duration',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'color_space' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.color_space',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('RGB', 'RGB'),
					array('CMYK', 'CMYK'),
					array('CMY', 'CMY'),
					array('YUV', 'YUV'),
					array('Grey', 'grey'),
					array('indexed', 'indx'),
				),
				'default' => '',
				'readOnly' => TRUE,
			)
		),
		'width' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.width',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'height' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.height',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'unit' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.unit',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('px', 'px'),
					array('mm', 'mm'),
					array('cm', 'cm'),
					array('m', 'm'),
					array('p', 'p'),
				),
				'default' => '',
				'readOnly' => TRUE,
			),
		),
		'is_variant' => array(
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.is_variant',
			'config' => array(
				'type' => 'check',
				'default' => '1',
				'readOnly' => TRUE,
			)
		),
		'fe_groups' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.fe_groups',
			'config' => array(
				'type' => 'select',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 9999,
				'autoSizeMax' => 30,
				'multiple' => 0,
				'foreign_table' => 'fe_groups',
				'MM' => 'sys_file_fegroups_mm',
			),
		),
	),
	'grid' => array(

		# Not used yet. Check what to do!
		#'sorting' => array(
		#	'tstamp' => 'DESC',
		#	'title' => 'ASC',
		#),
		'facets' => array(
			'uid',
			'title' => array(
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:title',
			),
			'extension',
			'name',
			'categories',
			'fe_groups',
		),
		'columns' => array(
			'__checkbox' => array(
				'width' => '5px',
				'sortable' => FALSE,
				'html' => '<input type="checkbox" class="checkbox-row-top" autocomplete="off"/>',
			),
			'uid' => array(
				'visible' => FALSE,
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:uid',
				'width' => '5px',
			),
			'identifier' => array(
				'visible' => FALSE,
			),
			'fileinfo' => array(
				'sortable' => FALSE,
				'renderer' => 'TYPO3\CMS\Media\GridRenderer\Preview',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:preview',
				'wrap' => '<div class="center preview">|</div>',
				'width' => '150px',
			),
			'title' => array(
				#'renderer' => 'TYPO3\CMS\Media\GridRenderer\Title',
				#'wrap' => '<span class="media-title">|</span>',
				'width' => '400px',
				'editable' => TRUE,
			),
			'description' => array(
				'visible' => FALSE,
				'editable' => TRUE,
			),
			'tstamp' => array(
				'visible' => FALSE,
				'format' => 'date',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:sys_file.tstamp',
			),
			'keywords' => array(
				'visible' => FALSE,
			),
			'categories' => array(
				'visible' => TRUE,
				'sortable' => FALSE,
				'renderers' => array(
					'TYPO3\CMS\Vidi\GridRenderer\RelationCreate',
					# The Media category renderer is faster to render but display less info...
					#'TYPO3\CMS\Media\GridRenderer\Category',
					'TYPO3\CMS\Vidi\GridRenderer\Relation',
				),
			),
			'usage' => array(
				'visible' => FALSE,
				'sortable' => FALSE,
				'renderer' => 'TYPO3\CMS\Media\GridRenderer\Usage',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:usage',
			),
			'fe_groups' => array(
				'visible' => TRUE,
				'renderers' => array(
					'TYPO3\CMS\Vidi\GridRenderer\RelationCreate',
					# Decide what grid renderer must be used "PermissionFe" or "Relation"
					#'TYPO3\CMS\Media\GridRenderer\PermissionFe',
					'TYPO3\CMS\Vidi\GridRenderer\Relation',
				),
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permissions_fe_groups',
				'sortable' => FALSE,
			),
			'variants' => array(
				'visible' => TRUE,
				'sortable' => FALSE,
				'renderer' => 'TYPO3\CMS\Media\GridRenderer\Variant',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants',
			),
			'status' => array(
				'visible' => FALSE,
				'renderer' => 'TYPO3\CMS\Media\GridRenderer\Status',
				'width' => '5%',
			),
			# un-comment me to see the "visible" flag in the grid.
			#'visible' => array(
			#	'renderer' => 'TYPO3\CMS\Media\GridRenderer\Visibility',
			#	'label' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:visibility_abbreviation',
			#	'width' => '3%',
			#),
			'creator' => array(
				'visible' => FALSE,
			),
			'creation_date' => array(
				'visible' => FALSE,
				'format' => 'datetime',
			),
			'modification_date' => array(
				'visible' => FALSE,
				'format' => 'datetime',
			),
			'__buttons' => array(
				'sortable' => FALSE,
				'width' => '70px',
			),
		)
	)
);

return \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($GLOBALS['TCA']['sys_file'], $tca);
?>