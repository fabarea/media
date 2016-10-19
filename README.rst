=======================================================================
Media for TYPO3 CMS |badge_travis| |badge_scrutinizer| |badge_coverage|
=======================================================================

.. |badge_travis| image:: https://travis-ci.org/fabarea/media.svg?branch=master
    :target: https://travis-ci.org/fabarea/media

.. |badge_scrutinizer| image:: https://scrutinizer-ci.com/g/fabarea/media/badges/quality-score.png?b=master
   :target: https://scrutinizer-ci.com/g/fabarea/media

.. |badge_coverage| image:: https://scrutinizer-ci.com/g/fabarea/media/badges/coverage.png?b=master
   :target: https://scrutinizer-ci.com/g/fabarea/media

Media is a tool for managing Assets for TYPO3 CMS 6.2 and is logically built on the top of FAL.
The File Abstraction Layer API was introduced in TYPO3 6.0 and allows to handle files in centralised way across the CMS.
Basically, Media provides the following set of features:

* Advanced metadata handling of Assets
* A user friendly BE module
* Mass upload of files and post processing of files
* Multi language handling of metadata
* Integration with the text editor (RTE)

.. image:: https://raw.github.com/fabarea/media/master/Documentation/MediaModule-01.png

General View of the BE module. Latest version of Media makes use of the folder tree. This can be activated / deactivated in the Extension Manager.
Notice that files can **be browsed recursively** by clicking the checkbox on the top right:

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-02.png


Fields can be displayed / hidden by picking the available columns in the drop down menu.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-01.png


Advanced searching of files, by title, by categories, by usage, etc... Criteria can be cumulated.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-03.png


Inline editing is as simple as clicking in the cell.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-04.png


Translation of files can be done inline.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-05.png


**Mass edit** the metadata of the files. Editable columns have an editing icon on the top where to edit a bunch of files.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-06.png


Export selected data to various format: CSV, XML, XLS

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-07.png


A bunch of tools for the admin related to files: find duplicate files, thumbnail pre-generation, etc...

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Intro-08.png


Project info and releases
=========================

Home page of the project: https://github.com/fabarea/media

Stable version released on TER: http://typo3.org/extensions/repository/view/media

Development version from Git:

::

	cd typ3conf/ext
	git clone https://github.com/fabarea/media.git

Flash news are also announced on http://twitter.com/fudriot

Installation
============

Download the source code either from the `Git repository`_ or from the TER for the stable versions. Install the extension as normal in the Extension Manager.

.. _Git repository: https://github.com/fabarea/media.git

Configuration
=============

Some settings, such as default categories applied upon upload, are global and must be configured in the settings of Media in the Extension Manager.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/ExtensionManager-01.png


Enable for the Editor User the correct permissions in the Backend User Group so that it can access the Media module and be able to upload files.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/BackendUserGroup-01.png


Besides, since Media is multi-storage capable, many settings are to be configured per storage. Make sure they are correctly set.
This will not be a problem for new storage created after Media is installed, they will have default values.
However, for existing storage, they will be no value.

Edit the settings of a Storage:

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Manual-02.png

Apply different upload settings:

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Manual-03.png

Configured target folder for each file type:

 .. image:: https://raw.github.com/fabarea/media/master/Documentation/Manual-04.png

User TSConfig
-------------

You can use the option `options.vidi.enableMediaFilePicker` to control the display of the File Picker of Media. Pay attention, this
option is only taken into consideration **if** the File Picker is activated in the Extension Manager first. The File Picker is the
popup displayed to connect a File with a content element (like image) or with other kind of records. The Core popup is however used
by default for the purpose of the folder tree. In the Media popup, the folder tree will not be displayed because of iframe issue.
As tip, use the recursive browsing in Media to retrieve all your files::

	# Define whether to use default file picker or the one from Media (default = 1) but
	options.vidi.enableMediaFilePicker = 1


Suhosin
-------

Please note that PHP setups with the Suhosin patch activated will have a default limit of 100 maximum number of variables that are allowed to be passed in the URL. This limit must be increased to 140::

	suhosin.get.max_vars = 140

How to customize the Grid in Media module
=========================================

Important to notice the Media BE module is powered by `Vidi`_ which is a List Component for TYPO3 CMS. To know more about Vidi
and how to configure the Grid, refer to the `Grid chapter`_.

.. _Vidi: https://github.com/fabarea/vidi
.. _Grid chapter: https://github.com/fabarea/vidi#tca-grid

View Helpers
============

Display list of files of category X
-----------------------------------

You can make use of a View Helper to retrieve a bunch of files on the Frontend. Let say we want
to display a list of files "png" images coming from the storage "1" along with the associated categories.
The code could look like this in your Fluid template::

	<strong>Number of files: {v:content.count(matches: {storage: 1}, type: 'sys_file')}</strong>

	<f:if condition="{v:content.find(matches: {storage: 1}, type: 'sys_file')}">
		<ul>
			<f:for each="{v:content.find(matches: '{storage: 1}', type: 'sys_file')}" as="file">
				<li>
					{file.uid} -
					{file.metadata.title} -
					<m:file.thumbnail file="{file}" output="imageWrapped"/>

					<f:if condition="{file.metadata.categories}}">
						<ul>
							<f:for each="{file.metadata.categories}" as="category">
								<li>{category.title}</li>
							</f:for>
						</ul>
					</f:if>
				</li>
			</f:for>
		</ul>
	</f:if>

	{namespace m=Fab\Media\ViewHelpers}
	{namespace v=Fab\Vidi\ViewHelpers}


A more complex example here, we want to retrieve the same as before but all files belonging to categories 1,2 sorted by title as addition.
We must provide "aliases" as workaround since Fluid would not parse the expression ``matches: {metadata.categories: '1,2'}`` and will return an exception.

::

	<f:for each="{v:content.find(
						matches: {storage: 1, extension: 'png', categories: '1,2'},
						orderings: {title: 'ASC'},
						type: 'sys_file',
						aliases: {categories: 'metadata.categories', title: 'metadata.title'}
					)}"
	       as="file">

		<li>..</li>
	</f:for>

Alternatively:

::

	<f:for each="{v:content.find(selection: 'my-selection')}" as="file">

		<li>..</li>
	</f:for>


The same can be achieved in a programming way::

	// Get the Content Repository for sys_file.
	$contentRepository = \Fab\Vidi\Domain\Repository\ContentRepositoryFactory::getInstance('sys_file');

	// Initialize a Matcher object.
	/** @var \Fab\Vidi\Persistence\Matcher $matcher */
	$matcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Vidi\Persistence\Matcher');

	// Add some criteria.
	$matcher->equals('storage', '1');
	$matcher->equals('metadata.categories', '1'); // "metadata" correspond to the join defined in the TCA of "sys_file".

	// etc... you can add more criteria as instance a "like"
	$matcher->like('metadata.title', 'foo');

	// Fetch the objects.
	$files = $contentRepository->findBy($matcher);


Thumbnail View Helper
---------------------

The thumbnail API is meant to render a preview of a file independently of its type (image, document, video, ...).
Notice, only thumbnail service for "image" and "document" is well implemented. Video
and audio are still on the todo list. In case no appropriate thumbnail service is found,
a fallback service is called generating a dummy thumbnail.

The Thumbnail View Helper can be used as follow::


	# The minimum required:
	<m:file.thumbnail file="{file}"/>

	# Give more settings to the thumbnail:
	<m:file.thumbnail file="{file}"
		configuration="{width: 800, height: 800}"
		attributes="{class: 'file-thumbnail'}"
		output="image"/>

	# Required attributes:
	# --------------------
	#
	# file="{file}"

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
	<m:file.thumbnail file="{file}" preset="image_medium"/>

	{namespace m=Fab\Media\ViewHelpers}

	# Or if your template contains ``<section />``,
	<html xmlns:f="http://typo3.org/ns/typo3/fluid/viewhelpers"
		xmlns:m="http://typo3.org/ns/Fab/Media/ViewHelpers">

		<section>
			<m:file.thumbnail file="{file}" preset="image_medium"/>
		</section>
    </html>


Besides the View Helper, a thumbnail can be generated in a programming way. The example illustrates some possibilities.
For more insight, refer to the class itself. Here we go::

	/** @var $thumbnailService \Fab\Media\Thumbnail\ThumbnailService */
	$thumbnailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailService', $file);
	$thumbnail = $thumbnailService
		->setConfiguration($configuration)
		->setOutputType(\Fab\Media\Thumbnail\ThumbnailInterface::OUTPUT_IMAGE_WRAPPED)
		->setAppendTimeStamp(TRUE)
		->create();

	print $thumbnail
	<a href="..." target="_blank">
		<img src="..." alt="..." title="..." />
	</a>


Media Tools
===========

Tools are registered through the Tool API provided by Vidi in ``ext_tables.php`` and can be accessed by clicking the upper right icon in the BE
module. Those tools are visible for Admin only::

	\Fab\Vidi\Tool\ToolRegistry::getInstance()->register('sys_file', 'Fab\Media\Tool\MissingFilesFinderTool');

Analyse File index
------------------

Admin Users have access to a BE module allowing to analyse the index of files.
The tool will search for missing files as well as duplicate entries in the database.

The module can be accessed by clicking on the top right icon
of the main module. Notice, the same actions can also be performed by CLI and will send
a email as report if anything is wrong.::

	./typo3/cli_dispatch.phpsh extbase missingFiles:analyse
	./typo3/cli_dispatch.phpsh extbase duplicateFiles:analyse
	./typo3/cli_dispatch.phpsh extbase duplicateRecords:analyse

Tip! Configure a Scheduler Task (under Extbase task) for regularly checking the index and detecting problem early enough.

Generate thumbnails
-------------------

Generate a bunch of thumbnails in advance to speed up the output of the Media BE module.
This process can take a lot of time. Prefer to run the CLI command::

	./typo3/cli_dispatch.phpsh extbase thumbnail:generate


File Upload API
===============

In the BE module, File upload is handled by `Fine Uploader`_ which is a Javascript plugin aiming to bring a user-friendly file uploading experience over the web.
The plugin relies on HTML5 technology which enables Drag & Drop from the Desktop as instance.

On the server side, there is an API which transparently handles whether the file come from an XHR request or a POST request.

::

		# Code below is simplified for the documentation sake.
		# Check out for more insight EXT:media/Classes/Controller/AssetController.php @ uploadAction

		/** @var $uploadManager \Fab\Media\FileUpload\UploadManager */
		$uploadManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Media\FileUpload\UploadManager');
		try {
			/** @var $uploadedFileObject \Fab\Media\FileUpload\UploadedFileInterface */
			$uploadedFileObject = $uploadManager->handleUpload();
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}

		$newFileObject = $targetFolder->addFile($uploadedFileObject->getFileWithAbsolutePath(), $uploadedFileObject->getName());

.. _Fine Uploader: http://fineuploader.com/


Image Optimizer API
===================

When a image is uploaded, there is a post-processing step where the image can be optimized.
By default there are two pre-configured optimizations: **resize** and **rotate**. The **resize** processing will
reduce the size of an image in case it exceeds a certain dimension. The maximum dimension allowed is to be configured per storage.
The **rotate** optimizer read the `exif`_ metadata and automatically rotates the image. For the auto-rotation features, credits go to
Xavier Perseguers where great inspiration was found in one of his `extension`_.

If needed, it is possible to add additional custom optimizers. Notice that the class must implement an interface ``\Fab\Media\FileUpload\ImageOptimizerInterface`` and can be added with following code::

	\Fab\Media\FileUpload\ImageOptimizer::getInstance()->add('Fab\Media\FileUpload\Optimizer\Resize');

.. _exif: http://en.wikipedia.org/wiki/Exchangeable_image_file_format
.. _extension: https://forge.typo3.org/projects/extension-image_autoresize/

Permission Management
=====================

Permissions management is about controlling accessibility of a file. Permissions can be defined on each file under tab "Access" where to connect
a File to a Frontend Group.

.. image:: https://raw.github.com/fabarea/media/master/Documentation/Manual-05.png

Notice Media **delegates file permission to third party extensions**, such as extension naw_securedl_.
On the long term it should be considered to be used a secure local driver, however.

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


Basic Metadata Extractor
========================

As a basic metadata extractor service, Media will set a title when a file is uploaded **or** whenever the files get indexed
through the Scheduler task. The metadata title is basically derived from the file name e.g. ``my_report.pdf`` will
results as ``My report``. This should help your Editors coping with this metadata and save them some typing.
Of course, the title will only be set, if no value exists beforehand.
