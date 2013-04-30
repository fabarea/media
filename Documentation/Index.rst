========================
Media for TYPO3 CMS
========================

Media Management (media) is a tool for organizing Media files and retrieve them by categories, mime-types etc.
and is a `replacement of the DAM`_ built on the top of FAL for TYPO3 CMS 6 branch. This document will describe the various API
introduced by Media for its needs along to its configuration.

Development happens: https://forge.typo3.org/projects/extension-media/
Issue tracker: https://forge.typo3.org/projects/extension-media/issues
Mailing list: http://lists.typo3.org/cgi-bin/mailman/listinfo/typo3-dev Make sure to mention the word "media" in the subject.

.. _replacement of the DAM: http://buzz.typo3.org/teams/dam/article/new-features-in-dam-13-and-the-future-of-dam/


Overview
=============

@todo add some words here....


Installation
=================

Download the source code either from the `Git repository`_ to get the latest branch or from the TER for the stables releases. Install the extension as normal in the Extension Manager.

.. _Git repository: https://git.typo3.org/TYPO3v4/Extensions/media.git

Configuration
=================

Configuration is mainly provided in the Extension Manager and is pretty much self-explanatory. Check possible options there.

* In the the Variant tab, you can configure possible mount points per file type. A mount point can be considered as a sub folder within the storage where the files are going to be stored. This is useful if one wants the file to be stored elsewhere than at the root of the storage.

Widgets
=================

Carousel Widget
-------------------

By default, the View Helper generates a Carousel Gallery based on the markup of `Twitter Bootstrap`_
and is assuming jQuery to be loaded. Syntax is as follows::

	# Note categories attribute can be an array categories="{1,3}"
	<m:widget.carousel height="340" width="1200" categories="1,3" interval="2000" sort="sorting" order="desc"/>
	{namespace m=TYPO3\CMS\Media\ViewHelpers}


	# Required attributes:
	# --------------------
	#
	# No attribute is required. However if you don't define a category *all images* will be displayed from the repository. It may take long!!

	# Default values:
	# ---------------
	#
	# Max height of the image
	# height = 600
	#
	# Max width of the image
	# width = 600
	#
	# Categories to be taken as filter.
	# categories = array()
	#
	# Interval value of time between the slides. "O" means no automatic sliding.
	# interval = 0
	#
	# Whether to display the title and description or not.
	# caption = true
	#
	# The field name to sort out.
	# sort =
	#
	# The direction to sort.
	# order = asc


The underlying template can be overridden by TypoScript. The default configuration looks as::

	config.tx_extbase {
		view {
			widget {
				TYPO3\CMS\Media\ViewHelpers\Widget\CarouselViewHelper {
					# Assuming a template file is under ViewHelpers/Widget/Carousel/Index.html
					templateRootPath = EXT:media/Resources/Private/Templates
				}
			}
		}
	}

.. _Twitter Bootstrap: http://twitter.github.io/bootstrap/examples/carousel.html


Permission management
======================

Permissions management is about controlling read access of an Asset by the right User. Permission can be defined on each file under tab "Access" where to connect
an Asset to a Backend and / or a Frontend group. To properly activate permission, consider also:

* Backend: there is an setting that can be activated in the Extension Manager which influences the display of files within the BE module.
* Frontend, extension such as naw_securedl_ must be installed. It will rewrite all URL pointing to a file to be secured.
There is a Hook in EXT:media/Resources/Private/Php/user_secure_download.php taking care of security check.

.. _naw_securedl: http://typo3.org/extensions/repository/view/naw_securedl

RTE integration
=================

The extension is shipping two buttons that can be added into the RTE for (1) linking a document and (2) inserting images from the Media module.
The button name references are ``linkmaker`` and ``imagemaker`` respectively which can be added by TypoScript in TSConfig with the following line::

	# key where to define the visible buttons in the RTE
	toolbarOrder = bar, linkmaker, bar, imagemaker, ...

	-> Refer to the documentation of extension HtmlArea for more details.


Domain Model and Repository
=============================

We are following the recommendation_ of the Iana_for taking apart the media types. Model works as follows:


	---------------------
	|       File        |  -> Limited meta data handling (Model in Core)
	---------------------
	          |
	         \|/
	---------------------
	|       Asset        | -> Advance meta data handling (Model in Media)
	---------------------
	          |                      \                     \                     \                     \
	         \|/                     \|/                   \|/                   \|/                   \|/
	---------------------  ---------------------  ---------------------  ---------------------  ---------------------
	|       Text        | |       Image        | |       Audio        | |       Video        | |       Application   | -> with specific repository
	---------------------  ---------------------  ---------------------  ---------------------  ---------------------
	  (txt, html, ...)      (png, jpg, ...)        (mp3, ogg, ...)        (mp4, avi, ...)       (pdf, doc, ...)


Along to the Models, corresponding repositories can be used. The fundamental one,
is the Asset Repository which is the "four-wheel" repository. It can query any kind of media types. Consider the snippet::

	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	$assetRepository = $objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');

	$assetRepository->findAll()
	$assetRepository->findByUid($uid)
	$assetRepository->findBy*($value)  e.g findByType
	$assetRepository->findOneBy*($value)  e.g findOneByType
	$assetRepository->countBy*($value)  e.g countBy

There is also an option that can be passed whether you want to be returned objects (the default) or arrays::

	# Will return an array of array instead of an array of object
	$assetRepository->setRawResult(TRUE)->findAll();

Besides the Asset repository, it comes a few repositories for "specialized" media type. As instance, for an Photo Gallery you are likely to use the Image repository
which apply an implicit filter on Images. But there is more than that:

* Text repository for plain text files (txt, html, ...)
* Image repository
* Audio repository
* Video repository
* Application repository (pdf, odt, doc, ...)

.. _Iana: http://en.wikipedia.org/wiki/Internet_Assigned_Numbers_Authority
.. _recommendation:: http://www.iana.org/assignments/media-types

Thumbnail API
======================

As a first place, a thumbnail can be generated from the Asset object, like::

	# Get a thumbnail of the file.
	{asset.thumbnail}

	# Get a thumbnail of the file wrapped within a link pointing to the original file.
	{asset.thumbnailWrapped}

If the default thumbnail is not "sufficient", a View Helper can be used enabling to configure the thumbnail to be generated::

	# The minimum
	<m:thumbnail object="{asset}"/>

	# Pass more settings to the thumbnail to be rendered.
	<m:thumbnail object="{asset}" configuration="{width: 800, height: 800}" attributes="{class: 'file-variant'}" wrap="true"/>

	# Required attributes:
	# --------------------
	#
	# object="{asset}"

	# Default values:
	# ---------------
	#
	# configuration= array()
	# attributes = array()
	# wrap = FALSE
	# preset = NULL

	# Pass some preset as for the dimension. Values can be:
	# - image_thumbnail => '100x100'  (where maximum width is 100 and maximum height is 100)
	# - image_mini => '120x120'
	# - image_small => '320x320'
	# - image_medium => '760x760'
	# - image_large => '1200x1200'
	# - image_original => '1920x1920'
	<m:thumbnail object="{asset}" preset="image_medium"/>

	{namespace m=TYPO3\CMS\Media\ViewHelpers}

File Upload API
=================

File upload is handled by `Fine Uploader`_ which is a Javascript plugin aiming to bring a user-friendly file-uploading experience over the web.
The plugin relies on HTML5 technology which enables Drag & Drop from the Desktop. File transfer is achieved by Ajax if supported. If not,
a fall back method with classical file upload is used by posting the file. (Though, the legacy approach still need to be tested more thoroughly).

On the server side, there is an API for file upload which handles transparently whether the file come from an XHR request or a Post request.

::

		# Notice code is simplified from the real implementation.
		# For more detail check EXT:media/Classes/Controller/AssetController.php @ uploadAction

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');
		try {
			/** @var $uploadedFileObject \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
			$uploadedFileObject = $uploadManager->handleUpload();
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}

		$targetFolderObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getContainingFolder();
		$newFileObject = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $uploadedFileObject->getName());

.. _Fine Uploader: http://fineuploader.com/


Image Optimizer API
=====================

When a image get uploaded, there is a post-processing step where the image get the chance to be "optimized".
By default there are two optimizations run against the image: **resize** and **rotate**. The `resize` processing enables
to reduce the size of an image if a User uploads a too big image. The maximum size can be configured in the Extension Manager.
The `rotate` optimizer read the `exif`_ metadata and automatically rotates the image. For the auto-rotation features, credits go to
Xavier Perseguers where great inspiration was found in one of his `extension`_.

If needed, it is possible to add additional custom optimizers. Notice that the class must implement an interface ``\TYPO3\CMS\Media\FileUpload\ImageOptimizerInterface`` and can be added with following code::

	$uploadedFile = \TYPO3\CMS\Media\FileUpload\ImageOptimizer::getInstance()->add('TYPO3\CMS\Media\FileUpload\Optimizer\Resize');


.. _exif: http://en.wikipedia.org/wiki/Exchangeable_image_file_format
.. _extension: https://forge.typo3.org/projects/extension-image_autoresize/


Variants API
=================

A Variant is, as its name indicates, a variation of a file to be used in a different context as its original. It actually better works for images. Variants can be automatically created upon uploading a file and can be inserted into the RTE, as instance. This setting should be activated in the Extension Manager and is quite handy for having standardized size of images across the website.

In the object land, a Variant object make the join between the original file and the Variant file. Additionally, it also stores the variation. Consider a few examples.

Use the Variant Service for creating a Variant out of a File::


	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager;

	/** @var \TYPO3\CMS\Media\Service\Variant $variantService */
	$variantService = $objectManager->get('TYPO3\CMS\Media\Service\Variant');

	$configuration = array(
		'width' => 200, // corresponds to maxH, respectively maxW
		'height' => 200,
	);
	$variantObject = $variantService->create($assetObject, $configuration);

	print $variantObject->getOriginal()->getUid();
	print $variantObject->getVariant()->getUid();
	print $variantObject->getVariation();

Retrieving all Variants from an Asset::

	/** @var $asset \TYPO3\CMS\Media\Domain\Model\Asset */
	$variants = $asset->getVariants();

Retrieving one Variant object from the Variant Repository::

	/** @var $variantRepository \TYPO3\CMS\Media\Domain\Repository\VariantRepository */
	$variantRepository;

	/** @var $fileObject \TYPO3\CMS\Core\Resource\File */
	$fileObject;

	$variantObject = $variantRepository->findOneByVariant($fileObject);

	# Possible save of Variant object
	$this->variantRepository->update($variantObject);

TCA Service API
=================

This API enables to fetch info related to TCA in a programmatic way. Since TCA covers a very large set of data, the service is divided in types.
There are are four parts being addressed: table, field, grid and form. The "grid" part extends the TCA and is introduced for the need of the BE module of media.

* table: deal with the "ctrl" part of the TCA. Typical info is what is the label of the table name, what is the default sorting, etc...
* field: deal with the "columns" part of the TCA. Typical info is what configuration, label, ... has a field name.
* grid: deal with the "grid" part of the TCA.
* form: deal with the "types" (and possible "palette") part of the TCA. Get what field compose a record type.

The API is meant to be generic and can be re-use for every record type within TYPO3. Find below some code example making use of the service factory.

Instantiate a TCA service related to **fields**::

	$tableName = 'sys_file';
	$serviceType = 'field';

	/** @var $fieldService \TYPO3\CMS\Media\Tca\FieldService */
	$fieldService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);

	// Refer to internal methods of the class.
	$fieldService->getFields();

Instantiate a TCA service related to **table**::

	$tableName = 'sys_file';
	$serviceType = 'table';

	/** @var $tableService \TYPO3\CMS\Media\Tca\TableService */
	$tableService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);

	// Refer to internal methods of the class.
	$tableService->getLabel();

The same would apply for the other part: form and grid.

Grid TCA
=================

A grid is a list view of records typical of a Backend module. TCA was extended to describe how a grid and its columns columns should be rendered. Example::

	// Grid configuration
	$TCA['sys_file']['grid'] = array(
		'columns' => array(
			'__checkbox' => array(
				'width' => '5px',
				'sortable' => FALSE,
				'html' => '<input type="checkbox" class="checkbox-head"/>',
			),
			'name' => array(
				'sortable' => FALSE,
				'renderer' => 'TYPO3\CMS\Media\Grid\Preview',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:preview',
				'wrap' => '<div class="center">|</div>',
			),
			'title' => array(
				'wrap' => '<span class="media-title">|</span>',
			),
			'tstamp' => array(
				'visible' => FALSE,
				'format' => 'date',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:sys_file.tstamp',
			),
			'keywords' => array(
			),
			'__buttons' => array(
				'sortable' => FALSE,
			),
		)
	);

Columns
---------

What attribute can be composed within array cell "columns"?

* sortable - default TRUE - whether the column is sortable or not.
* visible - default TRUE - whether the column is visible by default or hidden. There is a column picker on the GUI side controlling column visibility.
* renderer - default NULL - a class name to pass implementing
* label - default NULL - an optional label overriding the default label of the field - i.e. the label from TCA['tableName']['columns']['fieldName']['label']
* wrap - default NULL - a possible wrapping of the content. Useful in case the content of the cell should be styled in a special manner.
* width - default NULL - a possible width of the column

System columns
-----------------

There a few columns that are considered as "system" which means they don't correspond to a field but must be display to control the     GUI. By convention, theses columns are prefixed
with a double underscore e.g "__":

* __number: display a row number
* __checkbox: display a check box
* __buttons: display "edit", "deleted", ... buttons to control the row

