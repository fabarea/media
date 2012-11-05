<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_file');

$newFileTypes = array(
	'Text' => array('showitem' => 'storage, name, type, mime_type, sha1, size, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, content_creation_date, content_modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'Image' => array('showitem' => 'storage, name, type, mime_type, sha1, size, file, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, sys_language_uid, l10n_diffsource,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.geolocation, location_country, location_region, location_city, latitude, longitude,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, content_creation_date, content_modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'Audio' => array('showitem' => 'storage, name, type, mime_type, sha1, size, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, duration, unit,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo content_creation_date, content_modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'Video' => array('showitem' => 'storage, name, type, mime_type, sha1, size, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo, content_creation_date, content_modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'Software' => array('showitem' => 'storage, name, type, mime_type, sha1, size, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.temporalInfo content_creation_date, content_modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
);
$newFileTypes['5'] = $newFileTypes['Video'];
$newFileTypes['1'] = $newFileTypes['Audio'];
$newFileTypes['1'] = $newFileTypes['Image'];
$TCA['sys_file']['types'] = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule((array)$TCA['sys_file']['types'], $newFileTypes);
unset($newFileTypes);

$TCA['sys_file']['palettes'] = array(
	'1' => array('showitem' => 'status, ranking'),
	'10' => array('showitem' => 'width, height, unit', 'canNotCollapse' => '1'),
	'14' => array('showitem' => 'horizontal_resolution, vertical_resolution', 'canNotCollapse' => '1'),
);

$columns = array(
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
	'content_creation_date' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.content_creation_date',
		'config' => array(
			'type' => 'input',
			'size' => 12,
			'max' => 20,
			'eval' => 'datetime',
			'checkbox' => 1,
			'default' => time()
		),
	),
	'content_modification_date' => array(
		'exclude' => 1,
		'l10n_mode' => 'exclude',
		'l10n_display' => 'defaultAsReadonly',
		'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.content_modification_date',
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
	'sorting' => array(
		'tstamp' => 'DESC',
		'title' => 'ASC',
		'renderers' => array(),
	),
	'filter' => array(

	),
	'columns' => array(
		array(
			'internal_type' => TRUE,
			'field' => '_number',
			'sortable' => FALSE
		),
		array(
			'field' => 'title',
			'wrap' => '<span class="media-title">|</span>',
		),
		array(
			'field' => 'tstamp',
			'visible' => FALSE,
			'format' => 'date',
		),
		array(
			'field' => 'keywords',
		),
		array(
			'internal_type' => TRUE,
			'field' => '_buttons',
			'sortable' => FALSE
		),
//		'check' => array(),
//		'preview' => array(),
//		'categories' => array(),
//		'status' => array(),
//		'file_name' => array(),
//		'language' => array(),
//		'author' => array(),
//		'permission' => array(),
//		'content_creation_date' => array(),
//		'content_modification_date' => array(),
	)
);

// Searchable field
$TCA['sys_file']['ctrl']['searchFields'] = 'uid,title,keywords';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file', $columns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file', 'variants', '', 'after:type');
?>