<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_file');

$newFileTypes = array(
	TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload, fileinfo, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.geolocation, location_country, location_region, location_city, latitude, longitude,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.variants, variants,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload, fileinfo, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.variants, variants,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload, fileinfo, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.geolocation, location_country, location_region, location_city, latitude, longitude,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.variants, variants,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload, fileinfo, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, duration, unit,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.variants, variants,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload, fileinfo, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.variants, variants,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	TYPO3\CMS\Core\Resource\File::FILETYPE_SOFTWARE => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload, fileinfo, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.variants, variants,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
);

$TCA['sys_file']['types'] = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule((array)$TCA['sys_file']['types'], $newFileTypes);
unset($newFileTypes);

$TCA['sys_file']['palettes'] = array(
	'1' => array('showitem' => 'status, ranking'),
	'10' => array('showitem' => 'width, height, unit', 'canNotCollapse' => '1'),
	'14' => array('showitem' => 'horizontal_resolution, vertical_resolution', 'canNotCollapse' => '1'),
);

$columns = array(
	'fileinfo' => array(
		'config' => array(
			'type' => 'user',
			'userFunc' => 'EXT:media/Classes/Backend/TceForms.php:TYPO3\CMS\Media\Backend\TceForms->renderFileInfo',
		),
	),
	'variants' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.variants',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.status',
		'config' => array(
			'type' => 'select',
			'items' => array(
				array(
					'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.status.1',
					1,
					\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/status_1.png'
				),
				array(
					'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.status.2',
					2,
					\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/status_2.png'
				),
				array(
					'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.status.3',
					3,
					\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Icons/status_3.png'
				),
			),
		),
	),
	'title' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.title',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		)
	),
	'description' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.description',
		'config' => array(
			'type' => 'text',
			'cols' => 40,
			'rows' => 15,
			'eval' => 'trim'
		),
	),
	'keywords' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.keywords',
		'config' => array(
			'type' => 'text',
			'cols' => 40,
			'rows' => 15,
			'eval' => 'trim'
		),
	),
	'extension' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.extension',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.creation_date',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.modification_date',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.creator_tool',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'download_name' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.download_name',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.creator',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.publisher',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'source' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.source',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'alternative' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.alternative',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'caption' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.caption',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.pages',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'note' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.note',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.location_country',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'location_region' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.location_region',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'location_city' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.location_city',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'latitude' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.latitude',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.longitude',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.ranking',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.language',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.duration',
		'config' => array(
			'type' => 'input',
			'size' => '10',
			'max' => '20',
			'eval' => 'int',
			'default' => '0'
		)
	),
	'horizontal_resolution' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.horizontal_resolution',
		'config' => array(
			'type' => 'input',
			'size' => '10',
			'max' => '20',
			'eval' => 'int',
			'default' => '0'
		)
	),
	'vertical_resolution' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.vertical_resolution',
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
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.color_space',
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
			'default' => ''
		)
	),
	'width' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.width',
		'config' => array(
			'type' => 'input',
			'size' => '10',
			'max' => '20',
			'eval' => 'int',
			'default' => '0'
		),
	),
	'height' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.height',
		'config' => array(
			'type' => 'input',
			'size' => '10',
			'max' => '20',
			'eval' => 'int',
			'default' => '0'
		),
	),
	'unit' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.unit',
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
			'default' => ''
		),
	),
);

// Grid configuration
$TCA['sys_file']['grid'] = array(
	# Not used yet. Check what to do!
	#'sorting' => array(
	#	'tstamp' => 'DESC',
	#	'title' => 'ASC',
	#	'renderers' => array(),
	#),
	#'filter' => array(),
	'columns' => array(
//		'__checkbox' => array(),
		'__number' => array(
			'width' => '5px',
			'sortable' => FALSE,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:number',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:tx_media.tstamp',
		),
		'keywords' => array(
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
		'language' => array(
			'visible' => FALSE,
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