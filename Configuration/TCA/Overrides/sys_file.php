<?php

use Fab\Media\Facet\ActionPermissionFacet;
use Fab\Media\Facet\NumberOfReferencesFacet;
use Fab\Media\Facet\TypeFacet;
use Fab\Media\Grid\CategoryRenderer;
use Fab\Media\Grid\FrontendPermissionRenderer;
use Fab\Media\Grid\MetadataRenderer;
use Fab\Media\Grid\PreviewRenderer;
use Fab\Vidi\Facet\StandardFacet;
use Fab\Vidi\Grid\ButtonGroupRenderer;
use Fab\Vidi\Grid\CheckBoxRenderer;
use Fab\Vidi\Grid\RelationEditRenderer;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$tca = [
    'ctrl' => [
        // Beware that "metadata.categories" is quite expansive performance wise.
        'searchFields' => 'uid, extension, name, metadata.title, metadata.description, metadata.categories',
    ],
    'columns' => [
        'extension' => [
            'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:sys_file.extension',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'eval' => 'trim',
            ],
        ],
        'number_of_references' => [
            'config' => [
                'type' => 'input',
                'size' => 255,
                'readOnly' => true,
                'eval' => 'int',
            ],
        ],
    ],
    'vidi' => [
        // For actions such as update, remove, copy, move, the DataHandler of the Core is configured to be used by default.
        // It will work fine in most cases. However, there is the chance to set your own Data Handler if there are special needs (@see FileDataHandler in EXT:media)
        // Another reasons, would be for speed. You will notice a performance cost when mass editing data using the Core DataHandler.
        // Using your own DataHandler would make the mass processing much faster.
        'data_handler' => [
            // For all actions
            '*' => 'Fab\Media\DataHandler\FileDataHandler'
        ],
    ],
    'grid' => [
        'excluded_fields' => 'number_of_references, missing',
        'facets' => [
            'metadata.title',
            'metadata.categories',
            'name',
            StandardFacet::class => [
                'name' => 'extension',
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:sys_file.extension'
            ],
            'metadata.description',
            'identifier',
            NumberOfReferencesFacet::class => [],
            TypeFacet::class => [],
            ActionPermissionFacet::class => [],
            'uid',
        ],
        'columns' => [
            '__checkbox' => [
                'renderer' => CheckBoxRenderer::class,
            ],
            'uid' => [
                'visible' => false,
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:uid',
                'width' => '5px',
            ],
            'identifier' => [
                'visible' => false,
            ],
            'fileinfo' => [
                'renderer' => PreviewRenderer::class,
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:preview',
                'wrap' => '<div class="center preview">|</div>',
                'width' => '150px',
                'sortable' => false,
            ],
            'metadata.title' => [
                'renderer' => MetadataRenderer::class,
                'rendererConfiguration' => [
                    'property' => 'title',
                ],
                'width' => '400px',
                'editable' => true,
                'sortable' => true,
            ],
            'metadata.description' => [
                'renderer' => MetadataRenderer::class,
                'rendererConfiguration' => [
                    'property' => 'description',
                ],
                'visible' => false,
                'sortable' => false,
            ],
            'tstamp' => [
                'visible' => false,
                'format' => 'Fab\Vidi\Formatter\Date',
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:sys_file.tstamp',
            ],
            'metadata.categories' => [
                'renderers' => [
                    RelationEditRenderer::class,
                    CategoryRenderer::class,
                ],
                'editable' => true,
                'visible' => true,
                'sortable' => false,
            ],
            'usage' => [
                'renderer' => 'Fab\Media\Grid\UsageRenderer',
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:usage',
                'visible' => true,
                'sortable' => false,
            ],
            'metadata' => [
                'label' => 'Metadata File Identifier',
                'renderer' => MetadataRenderer::class,
                'rendererConfiguration' => [
                    'property' => 'uid',
                ],
                'visible' => false,
                'sortable' => false,
            ],
            '__action_permission' => [
                'renderer' => 'Fab\Media\Grid\ActionPermissionColumn',
                'visible' => false,
                'sortable' => false,
                'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permission',
            ],
        ]
    ]
];

// Add more info to the Grid if EXT:filemetadata is loaded. Notice that the extension is not required but suggested.
if (ExtensionManagementUtility::isLoaded('filemetadata')) {
    $additionalTca = [
        'ctrl' => [
            'searchFields' => $tca['ctrl']['searchFields'] . ', metadata.keywords',
        ],
        'grid' => [
            'columns' => [
                'metadata.keywords' => [
                    'renderer' => MetadataRenderer::class,
                    'configuration' => [
                        'property' => 'keywords',
                    ],
                    'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.keywords',
                    'visible' => false,
                    'sortable' => false,
                ],
                'metadata.fe_groups' => [
                    'renderers' => [
                        RelationEditRenderer::class,
                        FrontendPermissionRenderer::class,
                    ],
                    'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permissions_fe_groups',
                    'visible' => false,
                    'sortable' => false,
                ],
                'metadata.status' => [
                    'renderer' => MetadataRenderer::class,
                    'rendererConfiguration' => [
                        'property' => 'status',
                    ],
                    'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.status',
                    'visible' => false,
                    'width' => '5%',
                    'sortable' => false,
                ],
                # un-comment me to see the "visible" flag in the grid.
                #'visible' => array(
                #	'renderer' => 'Fab\Media\Grid\VisibilityRenderer', @todo will not work out of the box after 6.2 migration
                #	'label' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:visibility_abbreviation',
                #	'width' => '3%',
                #),
                'metadata.creator_tool' => [
                    'renderer' => MetadataRenderer::class,
                    'rendererConfiguration' => [
                        'property' => 'creator_tool',
                    ],
                    'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.creator_tool',
                    'visible' => false,
                    'sortable' => false,
                ],
                'metadata.content_creation_date' => [
                    'renderer' => MetadataRenderer::class,
                    'rendererConfiguration' => [
                        'property' => 'content_creation_date',
                    ],
                    'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.content_creation_date',
                    'visible' => false,
                    'format' => 'datetime',
                    'sortable' => false,
                ],
                'metadata.content_modification_date' => [
                    'renderer' => MetadataRenderer::class,
                    'rendererConfiguration' => [
                        'property' => 'content_modification_date',
                    ],
                    'label' => 'LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.content_modification_date',
                    'visible' => false,
                    'format' => 'datetime',
                    'sortable' => false,
                ],
            ]
        ]
    ];
    ArrayUtility::mergeRecursiveWithOverrule($tca, $additionalTca);
}

// Control buttons such as edit, delete, etc... must be set at the end in any case.
$tca['grid']['columns']['__buttons'] = [
    'renderer' => ButtonGroupRenderer::class,
];

ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file'], $tca);
