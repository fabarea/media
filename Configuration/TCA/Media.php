<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('sys_file');

$TCA['sys_file']['types'] = array(
	'0' => array('showitem' => 'mount, file'),

	'1' => array('showitem' => 'file, thumbnail, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'2' => array('showitem' => 'file, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.language, sys_language_uid, l10n_diffsource,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.geolocation, location_country, location_region, location_city, latitude, longitude,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.temporalInfo, creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'3' => array('showitem' => 'file, thumbnail, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.metrics, duration, unit,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.temporalInfo creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'4' => array('showitem' => 'file, thumbnail, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.temporalInfo creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),

	'5' => array('showitem' => 'file, thumbnail, l10n_parent, title, description, alternative, caption, keywords,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.visibility, hidden, status, ranking,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.language, sys_language_uid, l10n_diffsource, language,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher, source,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.temporalInfo creation_date, modification_date,
								--div--;LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tabs.file, download_name,
								--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
);

$TCA['sys_file']['palettes'] = array(
	'1' => array('showitem' => 'status, ranking'),
	'10' => array('showitem' => 'width, height, unit', 'canNotCollapse' => '1'),
	'14' => array('showitem' => 'horizontal_resolution, vertical_resolution', 'canNotCollapse' => '1'),
);

$columns = array(
		'file' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.file',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_file',
				'form_type' => 'user',
				'userFunc' => 'EXT:media/Classes/TCEforms/UserField.php:&Tx_Media_TCEforms_UserField->renderFile',
				'noTableWrapping' => TRUE,
				'readOnly' => TRUE,
			),
		),
		'thumbnail' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.thumbnail',
			'config' => array(
				'form_type' => 'user',
				'userFunc' => 'EXT:media/Classes/TCEforms/UserField.php:&Tx_Media_TCEforms_UserField->renderThumbnail',
				'noTableWrapping' => TRUE,
				'readOnly' => TRUE,
			),
		),
		'status' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.status',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.status.1',
						1,
						t3lib_extMgm::extRelPath('media') . 'Resources/Public/Icons/status_1.png'
					),
					array(
						'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.status.2',
						2,
						t3lib_extMgm::extRelPath('media') . 'Resources/Public/Icons/status_2.png'
					),
					array(
						'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.status.3',
						3,
						t3lib_extMgm::extRelPath('media') . 'Resources/Public/Icons/status_3.png'
					),
				),
			),
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			),
		),
		'keywords' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.keywords',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.creation_date',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.modification_date',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.creator_tool',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.download_name',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.creator',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.publisher',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'source' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.source',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'alternative' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.alternative',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'caption' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.caption',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.pages',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'note' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.note',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.location_country',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.location_region',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.location_city',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.latitude',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.longitude',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.ranking',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.language',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.duration',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.horizontal_resolution',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.vertical_resolution',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.color_space',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.width',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.height',
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
			'label' => 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media.unit',
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

t3lib_extMgm::addTCAcolumns('sys_file', $columns,1);
?>