===================
Media for TYPO3 CMS
===================

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

.. image:: https://raw.github.com/TYPO3-extensions/vidi/master/Documentation/Manual-01.png

Project info and releases
=============================

The home page of the project is at http://forge.typo3.org/projects/extension-media/

Stable version:
http://typo3.org/extensions/repository/view/media

Development version:
https://git.typo3.org/TYPO3CMS/Extensions/media.git

::

	git clone git://git.typo3.org/TYPO3CMS/Extensions/media.git


Github mirror:
https://github.com/TYPO3-extensions/vidi

Live website with pre-configured extension:
http://get.typo3.org/bootstrap

Flash news about latest development are also announced on:
http://twitter.com/fudriot

Installation
============

Download the source code either from the `Git repository`_ to get the latest branch or from the TER for the stable releases. Install the extension as normal in the Extension Manager.

.. _Git repository: https://git.typo3.org/TYPO3CMS/Extensions/media.git

Configuration
=============

Configuration is mainly provided in the Extension Manager and is pretty much self-explanatory. Check possible options there.

* In the the Variant tab, you can configure possible mount points per file type. A mount point can be considered as a sub folder within the storage where the files are going to be stored. This is useful if one wants the file to be stored elsewhere than at the root of the storage.

User TSConfig
-------------

Following option can be set::

	# Define whether to use default file picker or the one from Media (default = 1)
	options.vidi.enableMediaFilePicker = 1


Suhosin
-------

Please note that PHP setups with the suhosin patch installed will have a default limit of 100 maximum number of variables that may be registered through the URL. This limit must be increased to 140::

	suhosin.get.max_vars = 140



Thumbnail API
=============

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
===============

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
===================

When a image get uploaded, there is a post-processing step where the image get the chance to be "optimized".
By default there are two out-of-the-box optimizations: **resize** and **rotate**. The ``resize`` processing enables
to reduce the size of an image if a User uploads a too big image. The maximum size can be configured in the Extension Manager.
The ``rotate`` optimizer read the `exif`_ metadata and automatically rotates the image. For the auto-rotation features, credits go to
Xavier Perseguers where great inspiration was found in one of his `extension`_.

If needed, it is possible to add additional custom optimizers. Notice that the class must implement an interface ``\TYPO3\CMS\Media\FileUpload\ImageOptimizerInterface`` and can be added with following code::

	$uploadedFile = \TYPO3\CMS\Media\FileUpload\ImageOptimizer::getInstance()->add('TYPO3\CMS\Media\FileUpload\Optimizer\Resize');


.. _exif: http://en.wikipedia.org/wiki/Exchangeable_image_file_format
.. _extension: https://forge.typo3.org/projects/extension-image_autoresize/


Variants API
============

A Variant is, as its name indicates, a variation of a file and can have different roles such as "thumbnail", "subtitle", "caption".
Variants are mainly used in the RTE when an image of a different size of the original is created and inserted into the Editor.
For now Variants, can only be resized in the Image Editor but it is in the pipeline to also be able to crop it
and apply all sort of filters. There are settings in the Storage to automatically create Variants upon upload which
is a handy for having standardized size of images across the website.


Use the Variant Service for creating a Variant out of a File::

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
	$objectManager;

	/** @var \TYPO3\CMS\Media\Service\VariantService $variantService */
	$variantService = $objectManager->get('TYPO3\CMS\Media\Service\VariantService');

	$configuration = array(
		'width' => 200, // corresponds to maxH, respectively maxW
		'height' => 200,
	);
	$variant = $variantService->create($file, $configuration);

	print $variant->getOriginalResource()->getUid();
	print $variant->getUid();
	print $variant->getVariation();

Retrieving all Variants from an Asset::

	/** @var $asset \TYPO3\CMS\Media\Domain\Model\Asset */
	$variants = $asset->getVariants();

Retrieving all Variants of an original file using the Variant Repository::

	$variants = $variantRepository->findByOriginalFile($file);

Permission Management
=====================

Permissions management is about controlling accessibility of assets. Permissions can be defined on each file under tab "Access" where to connect
an Asset to a Frontend group.

* Frontend: Media **delegates file permission to third party extensions**. Media provides integration with extension naw_securedl_. The Hook is enabled by default in ``ext_localconf.php``. Once the extension is installed all URL pointing to a file will be rewritten.
* If using Apache, htaccess file is required for restricting direct access of a file.

.. _naw_securedl: http://typo3.org/extensions/repository/view/naw_securedl

RTE integration
===============

The extension is shipping two buttons that can be added into the RTE for (1) linking a document and (2) inserting images from the Media module.
The button name references are ``linkcreator`` and ``imageeditor`` respectively which can be added by TypoScript in Page / User TSConfig with the following line::


	# Snippet to paste as Page TSConfig
	# Module List > Right click on a page > tab "Resources" > field "Page TSConfig"
	RTE {

		// Default RTE configuration for all tables
		default {

			// Buttons to show
			showButtons := addToList(linkcreator,imageeditor)

			// Toolbar order
			toolbarOrder = bar, linkcreator, bar, imageeditor, ...
		}
	}
	# key where to define the visible buttons in the RTE
	toolbarOrder = bar, linkcreator, bar, imageeditor, ...

Refer to the `documentation`_ of extension HtmlArea for more details.

.. _documentation: http://docs.typo3.org/typo3cms/extensions/rtehtmlarea/Configuration/PageTsconfig/interfaceConfiguration/Index.html

Media View Helpers
==================

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



How to customize the Grid in Media module
=========================================

The grid is powered by extension "Vidi". Refer to the Vidi documentation for more insight.
https://github.com/TYPO3-Extensions/vidi#grid-tca


