Hidden documentation because it will not be working 100% following 6.2 migration.

Metadata View Helper
====================

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