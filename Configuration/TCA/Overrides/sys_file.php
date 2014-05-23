<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'ctrl' => array(
		'default_sortby' => 'uid DESC',
		'searchFields' => 'uid,extension,name', // sys_file_metadata.title,sys_file_metadata.keywords,
	),
	'grid' => array(
		'facets' => array(
			'uid',
			// @todo fix me!
			#'title' => array(
			#	'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:title',
			#),
			#new GenericFacetComponent($label)
			#new FacetComponent($label, $filterObject)
//			'usage' => array(
//				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:title',
//			),
			'extension',
			'name',
			#'categories',
			#'fe_groups',
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
				'renderer' => 'TYPO3\CMS\Media\Grid\PreviewRenderer',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:preview',
				'wrap' => '<div class="center preview">|</div>',
				'width' => '150px',
				'sortable' => FALSE,
			),
			'title' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'title')),
				'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_file.title',
				'dataType' => 'sys_file_metadata',
				'width' => '400px',
				'editable' => TRUE,
				'sortable' => FALSE,
			),
			'description' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'description')),
				'dataType' => 'sys_file_metadata',
				'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_file.description',
				'visible' => FALSE,
				'sortable' => FALSE,
			),
			'tstamp' => array(
				'visible' => FALSE,
				'format' => 'date',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:sys_file.tstamp',
			),
			'keywords' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'keywords')),
				'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.keywords',
				'dataType' => 'sys_file_metadata',
				'visible' => FALSE,
				'sortable' => FALSE,
			),
			'categories' => array(
				'renderers' => array(
					new \TYPO3\CMS\Media\Grid\RelationCreateRendererComponent(),
					new \TYPO3\CMS\Media\Grid\CategoryRendererComponent(),
				),
				'label' => 'LLL:EXT:lang/locallang_tca.xlf:sys_category.categories',
				'dataType' => 'sys_file_metadata',
				'visible' => TRUE,
				'sortable' => FALSE,
			),
			'usage' => array(
				'renderer' => 'TYPO3\CMS\Media\Grid\UsageRenderer',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:usage',
				'visible' => TRUE,
				'sortable' => FALSE,
			),
			'fe_groups' => array(
				'renderers' => array(
					new \TYPO3\CMS\Media\Grid\RelationCreateRendererComponent(),
					new \TYPO3\CMS\Media\Grid\FrontendPermissionRendererComponent(),
				),
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permissions_fe_groups',
				'dataType' => 'sys_file_metadata',
				'visible' => FALSE,
				'sortable' => FALSE,
			),
			'status' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'status')),
				'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.status',
				'dataType' => 'sys_file_metadata',
				'visible' => FALSE,
				'width' => '5%',
				'sortable' => FALSE,
			),
			# un-comment me to see the "visible" flag in the grid.
			#'visible' => array(
			#	'renderer' => 'TYPO3\CMS\Media\Grid\VisibilityRenderer', @todo will not work out of the box after 6.2 migration
			#	'label' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:visibility_abbreviation',
			#	'width' => '3%',
			#),
			'creator_tool' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'creator_tool')),
				'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.creator_tool',
				'dataType' => 'sys_file_metadata',
				'visible' => FALSE,
				'sortable' => FALSE,
			),
			'content_creation_date' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'content_creation_date')),
				'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.content_creation_date',
				'dataType' => 'sys_file_metadata',
				'visible' => FALSE,
				'format' => 'datetime',
				'sortable' => FALSE,
			),
			'content_modification_date' => array(
				'renderer' => new TYPO3\CMS\Media\Grid\MetadataRendererComponent(array('property' => 'content_modification_date')),
				'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.content_modification_date',
				'dataType' => 'sys_file_metadata',
				'visible' => FALSE,
				'format' => 'datetime',
				'sortable' => FALSE,
			),
			'metadata' => array(
				'label' => 'Metadata File Identifier',
				'visible' => FALSE,
				'force' => TRUE,
				'sortable' => FALSE,
			),
			'__buttons' => array(
				'sortable' => FALSE,
				'width' => '70px',
			),
		)
	)
);
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file'], $tca);