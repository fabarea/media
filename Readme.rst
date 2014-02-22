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

.. image:: https://raw.github.com/TYPO3-extensions/media/master/Documentation/Manual-01.png

Project info and releases
=============================

The home page of the project: http://forge.typo3.org/projects/extension-media/

Stable version released on TER: http://typo3.org/extensions/repository/view/media

Development version on Git: https://git.typo3.org/TYPO3CMS/Extensions/media.git

::

	cd typ3conf/ext
	git clone git://git.typo3.org/TYPO3CMS/Extensions/media.git


Github mirror:
https://github.com/TYPO3-extensions/media

Live website with pre-configured extension: http://get.typo3.org/bootstrap

Flash news about latest development are also announced on: http://twitter.com/fudriot

Installation
============

Download the source code either from the `Git repository`_ or from the TER for the stable versions. Install the extension as normal in the Extension Manager.

.. _Git repository: https://git.typo3.org/TYPO3CMS/Extensions/media.git

Configuration
=============

Some settings, such as default categories applied upon upload, are global and must be configured in the settings of Media in the Extension Manager.

Besides, since Media is multi-storage capable, many settings are to be configured per storage. Make sure they are correctly set.
This will not be a problem for new storage created after Media is installed, they will have default values.
However, for existing storage, they will be no value.

Edit the settings of a Storage:

.. image:: https://raw.github.com/TYPO3-extensions/media/master/Documentation/Manual-02.png

Apply different upload settings:

.. image:: https://raw.github.com/TYPO3-extensions/media/master/Documentation/Manual-03.png

Configured target folder for each file type:

 .. image:: https://raw.github.com/TYPO3-extensions/media/master/Documentation/Manual-04.png

User TSConfig
-------------

Following option can be set::

	# Define whether to use default file picker or the one from Media (default = 1)
	options.vidi.enableMediaFilePicker = 1


Suhosin
-------

Please note that PHP setups with the suhosin patch activated will have a default limit of 100 maximum number of variables that are allowed to be passed in the URL. This limit must be increased to 140::

	suhosin.get.max_vars = 140


Thumbnail API
=============

The thumbnail API is meant to render a preview of a file independently of its type.
The main object to be instantiated is the ``ThumbnailService`` which delegates the rendering
of the thumbnail to the right sub-service according to the file type. In case no appropriate thumbnail service is found,
a fallback service is called. For now, only thumbnails for "image" and "application" are well implemented. Video
and audio service are still on the todo list.

A thumbnail can be generated from the Asset object as a first place, like::

	# Get a thumbnail of the file.
	{asset.thumbnail}

	# Get a thumbnail of the file wrapped within a link pointing to the original file.
	{asset.thumbnailWrapped}

If the default thumbnail through the object is not enough, which will likely be the case in the real world, the Thumbnail View Helper can offer more flexibility::

	# The minimum required:
	<m:thumbnail file="{asset}"/>

	# Give more settings to the thumbnail:
	<m:thumbnail file="{asset}"
		configuration="{width: 800, height: 800}"
		attributes="{class: 'file-thumbnail'}"
		output="image"/>

	# Required attributes:
	# --------------------
	#
	# file="{asset}"

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
	<m:thumbnail file="{asset}" preset="image_medium"/>

	{namespace m=TYPO3\CMS\Media\ViewHelpers}

	# Or if your template contains ``<section />``,
	# namespace declaration can be done with xmlns attribute as of TYPO3 6.1:
	<html xmlns:f="http://typo3.org/ns/typo3/fluid/viewhelpers"
		xmlns:m="http://typo3.org/ns/TYPO3/CMS/Media/ViewHelpers">

		<section>
			<m:thumbnail file="{asset}" preset="image_medium"/>
		</section>
    </html>


Let examine also how a thumbnail can be generated in a programming way. The example illustrates some possibilities but
does not show every combination. Refer to the class itself::

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

In the BE module, File upload is handled by `Fine Uploader`_ which is a Javascript plugin aiming to bring a user-friendly file uploading experience over the web.
The plugin relies on HTML5 technology which enables Drag & Drop from the Desktop as instance.

On the server side, there is an API which transparently handles whether the file come from an XHR request or a POST request.

::

		# Code below is simplified for the documentation sake.
		# Check out for more insight EXT:media/Classes/Controller/AssetController.php @ uploadAction

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

When a image is uploaded, there is a post-processing step where the image can be optimized.
By default there are two pre-configured optimizations: **resize** and **rotate**. The **resize** processing will
reduce the size of an image in case it exceeds a certain dimension. The maximum dimension allowed is to be configured per storage.
The **rotate** optimizer read the `exif`_ metadata and automatically rotates the image. For the auto-rotation features, credits go to
Xavier Perseguers where great inspiration was found in one of his `extension`_.

If needed, it is possible to add additional custom optimizers. Notice that the class must implement an interface ``\TYPO3\CMS\Media\FileUpload\ImageOptimizerInterface`` and can be added with following code::

	\TYPO3\CMS\Media\FileUpload\ImageOptimizer::getInstance()->add('TYPO3\CMS\Media\FileUpload\Optimizer\Resize');

.. _exif: http://en.wikipedia.org/wiki/Exchangeable_image_file_format
.. _extension: https://forge.typo3.org/projects/extension-image_autoresize/

Permission Management
=====================

Permissions management is about controlling accessibility of a file. Permissions can be defined on each file under tab "Access" where to connect
a File to a Frontend Group.

.. image:: https://raw.github.com/TYPO3-extensions/media/master/Documentation/Manual-05.png

Notice the following:

* Frontend: Media **delegates file permission to third party extensions**. Media provides integration with extension naw_securedl_. The Hook is enabled by default in ``ext_localconf.php``. Once the extension is installed all URL pointing to a file will be rewritten.
* Whenever Apache is used as webserver, a htaccess file is required for restricting direct access.

.. _naw_securedl: http://typo3.org/extensions/repository/view/naw_securedl

RTE integration
===============

The extension ships two buttons that can be added at the RTE level for (1) linking a file and (2) inserting an image as part of the content.
The button name references are ``linkcreator`` and ``imageeditor`` respectively which can be added by TypoScript in Page / User TSConfig::


	# Snippet to be copied / pasted in Page TSConfig
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


How to customize the Grid in Media module
=========================================

The grid is powered by `Vidi`_. Refer to the `Grid chapter`_ for more insight.

.. _Vidi: https://forge.typo3.org/projects/extension-vidi
.. _Grid chapter: https://github.com/TYPO3-Extensions/vidi#grid-tca


