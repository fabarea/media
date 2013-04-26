<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_file');

$newFileTypes = array(
	TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => array('showitem' => '
								fileinfo, title, description, keywords, alternative, caption,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:publish-dates;20;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metrics;70;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array('showitem' => '
								fileinfo, title, description, keywords, alternative, caption,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:publish-dates;20;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array('showitem' => '
								fileinfo, title, description, keywords, alternative, caption,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:publish-dates;20;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metrics;70;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array('showitem' => '

								fileinfo, title, description, keywords, alternative, caption,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:publish-dates;20;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									duration,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metrics;70;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array('showitem' => '
								fileinfo, title, description, keywords, alternative, caption,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:publish-dates;20;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									duration,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_SOFTWARE => array('showitem' => '
								fileinfo, title, description, keywords, alternative, caption,

								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility;10;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:publish-dates;20;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:metadata,
									creator,--palette--;;30;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:geo-location;40;; ,
									--palette--;;50;; ,
									--palette--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:temporal-info;60;; ,

								--div--;LLL:EXT:media/Resources/Private/Language/locallang.xlf:variants, variants,'),
);

$TCA['sys_file']['types'] = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule((array)$TCA['sys_file']['types'], $newFileTypes);
unset($newFileTypes);

$TCA['sys_file']['palettes'] = array(
	'10' => array('showitem' => 'hidden, status, ranking', 'canNotCollapse' => '1'),
	'20' => array('showitem' => 'starttime, endtime,', 'canNotCollapse' => '1'),
	'30' => array('showitem' => 'publisher, source', 'canNotCollapse' => '1'),
	'50' => array('showitem' => 'latitude, longitude', 'canNotCollapse' => '1'),
	'40' => array('showitem' => 'location_country, location_region, location_city', 'canNotCollapse' => '1'),
	'60' => array('showitem' => 'creation_date, modification_date', 'canNotCollapse' => '1'),
	'70' => array('showitem' => 'width, height, unit, color_space', 'canNotCollapse' => '1'),
);

$columns = array(
	'fileinfo' => array(
		'config' => array(
			'type' => 'user',
			'userFunc' => 'EXT:media/Classes/Backend/TceForms.php:TYPO3\CMS\Media\Backend\TceForms->renderFileUpload',
		),
	),
	'variants' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.variants',
		'config' => array(
			'type' => 'inline',
			'foreign_table' => 'sys_file_variants',
			'foreign_selector' => 'role',
			'foreign_field' => 'original'
		)
	),
	'starttime' => Array(
		'exclude' => 1,
		'l10n_mode' => 'mergeIfNotBlank',
		'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
		'config' => Array(
			'type' => 'input',
			'size' => '10',
			'max' => '20',
			'eval' => 'datetime',
			'checkbox' => '0',
			'default' => '0'
		)
	),
	'endtime' => Array(
		'exclude' => 1,
		'l10n_mode' => 'mergeIfNotBlank',
		'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
		'config' => Array(
			'type' => 'input',
			'size' => '8',
			'max' => '20',
			'eval' => 'datetime',
			'checkbox' => '0',
			'default' => '0',
			'range' => Array(
				'upper' => mktime(0, 0, 0, 12, 31, 2020),
				'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
			)
		)
	),
	'hidden' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
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
		'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_file.title',
		'config' => array(
			'type' => 'input',
			'size' => 255,
			'eval' => 'trim'
		)
	),
	'description' => array(
		'exclude' => 0,
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
			'type' => 'text',
			'cols' => 40,
			'rows' => 15,
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.alternative',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'caption' => array(
		'exclude' => 1,
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
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.location_country',
		'config' => array(
			'type' => 'input',
			'size' => 20,
			'eval' => 'trim'
		),
	),
	'location_region' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:sys_file.location_region',
		'config' => array(
			'type' => 'input',
			'size' => 20,
			'eval' => 'trim'
		),
	),
	'location_city' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
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
				array(1,1),
				array(2,2),
				array(3,3),
				array(4,4),
				array(5,5),
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
);

// Grid configuration
$TCA['sys_file']['grid'] = array(
	# Not used yet. Check what to do!
	#'sorting' => array(
	#	'tstamp' => 'DESC',
	#	'title' => 'ASC',
	#),
	#'filter' => array(),
	'columns' => array(
		'__checkbox' => array(
			'width' => '5px',
			'sortable' => FALSE,
			'html' => '<input type="checkbox" class="checkbox-row-top"/>',
		),
		'uid' => array(
			'visible' => FALSE,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:uid',
			'width' => '5px',
		),
		'name' => array(
			'sortable' => FALSE,
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Preview',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:preview',
			'wrap' => '<div class="center">|</div>',
			'width' => '150px',
		),
		'title' => array(
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Title',
			'wrap' => '<span class="media-title">|</span>',
			'width' => '400px',
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
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Category',
		),
		'usage' => array(
			'visible' => TRUE,
			'sortable' => FALSE,
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Usage',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:usage',
		),
		'permission' => array(
			'visible' => FALSE,
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Permission',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permission',
		),
		'status' => array(
			'visible' => FALSE,
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Status',
			'width' => '5%',
		),
		'hidden' => array(
			'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Visibility',
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:visibility_abbreviated',
			'width' => '3%',
		),
		'creator' => array(
			'visible' => FALSE,
		),
		'creation_date' => array(
			'visible' => FALSE,
			'format' => 'date',
		),
		'modification_date' => array(
			'visible' => FALSE,
			'format' => 'date',
		),

		'__buttons' => array(
			'sortable' => FALSE,
			'width' => '70px',
		),
	)
);

// Searchable field
$TCA['sys_file']['ctrl']['searchFields'] = 'uid,title,keywords,extension';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file', $columns, 1);

?>