========================
Media for TYPO3 CMS
========================

Media is a tool for managing Assets for TYPO3 CMS 6.1 and is logically built on the top of FAL. FAL, for those who are unfamiliar,
is a the File Abstraction Layer introduced in TYPO3 6.0 which enables it to handle files in centralised way across the CMS.
The basic idea of FAL is that every file has an entry in the database to leverage its use as an asset. Basically, Media provides the following set of features:

* Advanced metadata handling of Assets
* API for querying Image, Text, Audio, Video, Application from their repository
* A user friendly BE module
* Mass upload of files and post processing of files
* Multi language handling of metadata
* File permission management
* Automatic Metadata extraction upon upload provided by EXT:metadata
* Integration with the text editor (RTE)
* ...

Project info and releases
=============================

The home page of the project is at http://forge.typo3.org/projects/extension-media/

Stable version:
http://typo3.org/extensions/repository/view/media

Development version:
https://git.typo3.org/TYPO3v4/Extensions/media.git

::

	git clone git://git.typo3.org/TYPO3v4/Extensions/media.git

Live website with pre-configured extension:
http://get.typo3.org/bootstrap

Flash news about latest development are also announced on:
http://twitter.com/fudriot

Installation
=================

Download the source code either from the `Git repository`_ to get the latest branch or from the TER for the stable releases. Install the extension as normal in the Extension Manager.

.. _Git repository: https://git.typo3.org/TYPO3v4/Extensions/media.git

Configuration
=================

Configuration is mainly provided in the Extension Manager and is pretty much self-explanatory. Check possible options there.

* In the the Variant tab, you can configure possible mount points per file type. A mount point can be considered as a sub folder within the storage where the files are going to be stored. This is useful if one wants the file to be stored elsewhere than at the root of the storage.


Suhosin
--------

Please note that PHP setups with the suhosin patch installed will have a default limit of 100 maximum number of variables that may be registered through the URL. This limit must be increased to 140::

	suhosin.get.max_vars = 140


Domain Model and Repository
=============================

We are following the recommendation_ of the Iana_for taking apart the media types. Model works as follows::

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
is the Asset Repository which is the "four-wheel" repository. It can query any kind of media types. Although FAL is not using the Extbase persistence layer, the API is very close to what one would expect from it. Consider the snippet::

	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	$assetRepository = $objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');

	$assetRepository->findAll()
	$assetRepository->findByUid($uid)
	$assetRepository->findBy*($value)  e.g findByType
	$assetRepository->findOneBy*($value)  e.g findOneByType
	$assetRepository->countBy*($value)  e.g countBy

Look for assets given a search term::

	/** @var $matcher \TYPO3\CMS\Media\QueryElement\Matcher */
	$matcher = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Matcher');
	$matcher->setSearchTerm('foo');

	/** @var $assetRepository \TYPO3\CMS\Media\Domain\Model\AssetRepository */
	$assetRepository = $this->objectManager->get('TYPO3\CMS\Media\Domain\Model\AssetRepository');
	$assetRepository->findBy($matcher);


Look for assets given multiple categories::

	/** @var $matcher \TYPO3\CMS\Media\QueryElement\Matcher */
	$matcher = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Matcher');
	$matcher->addMatch('categories', $uidOrObject);
	$matcher->addMatch('categories', $uidOrObject2);

	/** @var $assetRepository \TYPO3\CMS\Media\Domain\Model\AssetRepository */
	$assetRepository = $this->objectManager->get('TYPO3\CMS\Media\Domain\Model\AssetRepository');
	$assetRepository->findBy($matcher);

	# Alternative syntax which for only one category

	/** @var $assetRepository \TYPO3\CMS\Media\Domain\Model\AssetRepository */
	$assetRepository = $this->objectManager->get('TYPO3\CMS\Media\Domain\Model\AssetRepository');
	$assetRepository->findByCategories($uidOrObject);

There is also an option that can be passed whether you want to be returned objects (the default) or arrays::

	# Will return an array of array instead of an array of object
	$assetRepository->setRawResult(TRUE)->findAll();


Besides the Asset repository, it comes a few repositories for "specialized" media types. As instance, for an Photo Gallery you are likely to use the Image repository
which apply an implicit filter on Images. But there is more than that with:

* Text repository for plain text files (txt, html, ...)
* Image repository
* Audio repository
* Video repository
* Application repository (pdf, odt, doc, ...)

.. _Iana: http://en.wikipedia.org/wiki/Internet_Assigned_Numbers_Authority
.. _recommendation:: http://www.iana.org/assignments/media-types

Thumbnail API
======================

The thumbnail API is meant for generating out of an asset a preview, regardless of its type. The entry point of the API is the
Thumbnail service class which then delegates the rendering of the thumbnail to the right sub service according to the asset
type. A strategy pattern is used to determine which sub service fits the best . In case no one is found,
a fallback thumbnail generator is used. For now, asset of type "image" and "application" are implemented. Video
and audio thumbnail service would still be on the todo list...

As a first place, a thumbnail can be generated from the Asset object, like::

	# Get a thumbnail of the file.
	{asset.thumbnail}

	# Get a thumbnail of the file wrapped within a link pointing to the original file.
	{asset.thumbnailWrapped}

If the default thumbnail is not enough, which likely will be the case, a View Helper can be used enabling to configure the
thumbnail to be generated::

	# The minimum
	<m:thumbnail object="{asset}"/>

	# Pass more settings to the thumbnail to be rendered.
	<m:thumbnail object="{asset}"
		configuration="{width: 800, height: 800}"
		attributes="{class: 'file-variant'}"
		output="image"/>

	# Required attributes:
	# --------------------
	#
	# object="{asset}"

	# Default values:
	# ---------------
	#
	# configuration= array()
	# attributes = array()
	# output = image (possible values: "uri", "image", "imageWrapped")
	# preset = NULL

	# Pass some preset as for the dimension. Values can be:
	# - image_thumbnail => '100x100'  (where maximum width is 100 and maximum height is 100)
	# - image_mini => '120x120'
	# - image_small => '320x320'
	# - image_medium => '760x760'
	# - image_large => '1200x1200'
	<m:thumbnail object="{asset}" preset="image_medium"/>

	{namespace m=TYPO3\CMS\Media\ViewHelpers}

	# Or if your template contains ``<section />``,
	# namespace declaration can be done with xmlns attribute as of TYPO3 6.1:
	<html xmlns:f="http://typo3.org/ns/typo3/fluid/viewhelpers"
		xmlns:m="http://typo3.org/ns/TYPO3/CMS/Media/ViewHelpers">

		<section>
			<m:thumbnail object="{asset}" preset="image_medium"/>
		</section>
    </html>


Let see also how we can generate a thumbnail in a programming way. The example emphasises some configuration to illustrate the
use of the API and does not show every configuration possibility. Refer to the class itself::

	/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
	$thumbnailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
	$thumbnail = $thumbnailService
		->setFile($file)
		->setConfiguration($configuration)
		->setOutputType(\TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE_WRAPPED)
		->setAppendTimeStamp(TRUE)
		->create();

	print $thumbnail;
	<a href="..." target="_blank">
		<img src="..." alt="..." title="..." />
	</a>

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
By default there are two out-of-the-box optimizations: **resize** and **rotate**. The `resize` processing enables
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

	/** @var \TYPO3\CMS\Media\Service\VariantService $variantService */
	$variantService = $objectManager->get('TYPO3\CMS\Media\Service\VariantService');

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


Indexing Service
======================

Admin Users have access to a BE module allowing to check the index of the storage.
It can be opened by clicking a special icon displayed on the top bar
of the main module. Notice The same actions can also be performed by CLI.
There are basically two commands that are explained below::

	# Indexing of all files within the Media storage
	# The command is also available as scheduler task for convenience.
	./typo3/cli_dispatch.phpsh extbase media:index

	# Detect whether a file is existing in the database but missing in the storage.
	# The tool can also detect duplicate file objects from the database::
	./typo3/cli_dispatch.phpsh extbase media:checkIndex


Permission management
======================

Permissions management is about controlling accessibility of assets. Permissions can be defined on each file under tab "Access" where to connect
an Asset to a Backend and / or a Frontend group. Beware activating the setting in the Extension Manager revert the logic of file access. Out of the box, all files are accessible by everyone (allowed by default policy). With permission enabled, only authorized users are able to access a resource (deny by default policy). Admin users still have access to all files, though. On the FE, permission handling is provided by third party extension. Media provides integration with EXT:naw_securedl. In order to enable permission, a few things must be activated:

* Backend: there is a flag to check in the Extension Manager in tab "security"
* Frontend: Media **delegates file permission to third party extensions**. Media provides integration with extension naw_securedl_. However the Hook is not enabled by default and must be commented out in ``ext_localconf.php``. Once the extension is installed all URL pointing to a file will be rewritten.
* If using Apache, htaccess file is required for restricting direct delivery of a file by the web server.

Current implementation is beta quality. Rough edges are to be expected. Secure images are not supported for instance but are in the pipeline. Also, important to mention, it was tested with master version of EXT:naw_securedl https://github.com/TYPO3-Extensions/naw_securedl and it looks a patch is required to be applied http://forge.typo3.org/issues/48269. Also, default setting "filetype" was changed removing images file types.

.. _naw_securedl: http://typo3.org/extensions/repository/view/naw_securedl

RTE integration
=================

The extension is shipping two buttons that can be added into the RTE for (1) linking a document and (2) inserting images from the Media module.
The button name references are ``linkcreator`` and ``imageeditor`` respectively which can be added by TypoScript in TSConfig with the following line::

	# key where to define the visible buttons in the RTE
	toolbarOrder = bar, linkcreator, bar, imageeditor, ...

	-> Refer to the documentation of extension HtmlArea for more details.


Media View Helpers
====================

Media ships a few View Helpers that are described below and can be considered part as the API.

Metadata
--------

A metadata VH is available for displaying in a flexible way meta information of a file such as width, height, size, ...

::

	{namespace m=TYPO3\CMS\Media\ViewHelpers}
	<m:metadata object="{asset}" format="%s x %s" properties="{width, height}" />

	# Will output: <div class="metadata">300 x 200</div>

	<m:metadata object="{asset}" format="%s K" properties="{size}" />

	# Will output: <div class="metadata">500 K</div>

	# With all options
	<m:metadata object="{asset}" format="%s K" properties="{size}" template="<div class='metadata'>%s</div>"
		configuration="{sizeUnit: 1000}"/>

	# Required attributes:
	# --------------------
	#
	# object, format, properties

	# Default values:
	# ---------------
	#
	# The object used as reference
	# object = NULL
	#
	# The format which should contain the placeholder "%s"
	# format = NULL
	#
	# What properties of object, must corresponds to the number of placeholder in the format
	# properties = array()
	#
	# The template used agains the formatting
	# template = NULL
	#
	# Possible configuration used internally
	# configuration = array()

Carousel Widget
-------------------

By default, the View Helper generates a Carousel Gallery based on the markup of `Twitter Bootstrap`_
and is assuming jQuery to be loaded. Syntax is as follows::

	# Note categories attribute can be an array categories="{1,3}"
	<m:widget.carousel height="340" width="1200" categories="1,3" interval="2000" sort="ranking" order="desc"/>
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
				'renderer' => 'TYPO3\CMS\Media\GridRenderer\Preview',
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

