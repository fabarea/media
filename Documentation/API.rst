Hidden documentation because it is now working 100% after 6.2 migration

Domain Model and Repository
===========================

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


Indexing Service
================

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


Carousel Widget
---------------

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