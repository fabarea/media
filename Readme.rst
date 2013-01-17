========================
Media for TYPO3 CMS
========================

Media Management (media) is a tool for organizing Media files and retrieve them by categories, mime-types etc.
and is, in this regard a pragmatic replacement to DAM build on the top of FAL. Read more about DAM `history and status`_.

.. _history and status: http://buzz.typo3.org/teams/dam/article/new-features-in-dam-13-and-the-future-of-dam/

Development happens: https://forge.typo3.org/projects/extension-media/
Issue tracker: https://forge.typo3.org/projects/extension-media/issues
Backlog: https://forge.typo3.org/rb/master_backlogs/extension-media
Mailing list: http://lists.typo3.org/cgi-bin/mailman/listinfo/typo3-dev Make sure to mention the word "media" in the subject.

Media introduces different API for its needs which are going to be roughly detailed below:

* File upload: how is the file upload managed in Media in correlation with FAL?
* Form API: how to create form elements, put it into a container and render all?
* TCA service API: how to query the TCA in a programming way. To know more about TCA : http://docs.typo3.org/typo3cms/TCAReference/
* TCA grid: extend the TCA for the need of a grid component (AKA management list).
* TCA widget: introduce random widgets within a form.

File Upload
=================

File upload is handled by `Fine Uploader`_ which is a Javascript plugin aiming to bring a user-friendly file-uploading experience over the web.
The plugin relies on HTML5 technology which enables Drag & Drop from the Desktop. File transfer is achieved by Ajax if supported. If not,
a fall back method with classical file upload is used by posting the file. (Though, the legacy approach still need to be tested more thoroughly).

On the server side, there is an API for file upload which handles transparently whether the file come from an XHR request or a Post request.

::

		# Notice code is simplified from the real implementation.
		# For more detail check EXT:media/Classes/Controller/MediaController.php @ uploadAction

		$uploadDirectory = '/somewhere/in/your/system';
		$conflictMode = 'overwrite';

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');
		try {
			/** @var $uploadedFileObject \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
			$uploadedFileObject = $uploadManager->handleUpload($uploadDirectory, $conflictMode);
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}

		# FAL integration.
		$temporaryFileName = $uploadDirectory . $uploadedFileObject->getName();
		$fileName = $uploadedFileObject->getName();

		$targetFolderObject = \TYPO3\CMS\Media\Utility\StorageFolder::get();
		$newFileObject = $targetFolderObject->addFile($temporaryFileName, $fileName , $conflictMode);

.. _Fine Uploader: http://fineuploader.com/


Form API
===========

With the conclusion that that TCEforms was unfortunately too monolithic to be re-used for a customized Media BE module and since I was not able to reuse the FORM object as such, a slim API was developed enabling to render a form elements. The final goal was to be able to write a Fluid ViewHelper which would render a form object based on its TCA.

As example::

	<m:form.tca object={media} />


For the styling `Twitter Bootstrap framework`_ was used giving the advantage to provide responsive capability out of the box.

Form element
--------------

The low level API enables to render a form in a programmatic way. It provides two different types of components: (form) elements and containers. A form element implements the "renderable" interface and can be textfield, textarea, etc. A container implements the "renderable" and also the "containable" interface and can be used for panels, tab-panels, etc. Let illustrate with examples:

Render a minimal text field::

	$fieldName = 'title';
	$value = 'foo';

	/** @var $fieldObject \TYPO3\CMS\Media\Form\TextField */
	$fieldObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\TextField');
	$fieldObject->setName($fieldName)->render()

Render a text field with label::

	$fieldName = 'title';
	$value = 'foo';

	/** @var $fieldObject \TYPO3\CMS\Media\Form\TextField */
	$fieldObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\TextField');
	$fieldObject->setName($fieldName)
		->setLabel($label)
		->setValue($value)
		->addAttribute(array('class' => 'span6'))
		->render();

Create and render a tab panel (container) ::

	/** @var $tabPanel \TYPO3\CMS\Media\FormContainer\TabPanel */
	$tabPanel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FormContainer\TabPanel');

	$tabPanel->createPanel($panelTitle)
		->render();

Create a tab panel, add a text field into it and render it::

	/** @var $fieldObject \TYPO3\CMS\Media\Form\TextField */
	$fieldObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\TextField');
	$fieldObject->setName($fieldName)
		->setLabel($label)
		->setValue($value)
		->setPrefix($this->getPrefix())
		->addAttribute(array('class' => 'span6'));

	/** @var $tabPanel \TYPO3\CMS\Media\FormContainer\TabPanel */
	$tabPanel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FormContainer\TabPanel');

	$tabPanel->createPanel($panelTitle)
		->addItem($fieldObject)
		->render();

.. _Twitter Bootstrap framework: http://twitter.github.com/bootstrap/


Form factory
--------------

The form factory API is useful for instantiating and returning Form object (cf Form API above). In that sense, it control the final output and make the bridge with TYPO3 CMS.

Limitation:

* no support yet for palette, radio button (should be easy) and inline editing,
* no language handling,
* no version handling.


The form object factory API looks very similar to the low level API (above) at the first glance and can been seen as helper to create form object without worrying of the field type.
By simply passing a field name, the form factory will return the correct object, ready to be rendered.

	/** @var $fieldObject \TYPO3\CMS\Media\Form\FormFieldInterface */
	$fieldName = 'title';
	$value = 'foo';

	# Create a field form object given a field name
	$fieldObject = $fieldFactory->setFieldName($fieldName)
		->setValue($value)
		->get();

	# Render the form
	$fieldObject->render();


TCA Service API
=================

This API enables to fetch info related to TCA in a programmatic way. Since TCA covers a very large set of data, the service is divided in types.
There are are four parts being addressed: table, field, grid and form. The "grid" part extends the TCA and is introduced for the need of media.

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
			'__number' => array(
				'sortable' => FALSE,
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:number',
			),
			'name' => array(
				'sortable' => FALSE,
				'renderer' => 'TYPO3\CMS\Media\Renderer\Grid\Preview',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:preview',
				'wrap' => '<div class="center">|</div>',
			),
			'title' => array(
				'wrap' => '<span class="media-title">|</span>',
			),
			'tstamp' => array(
				'visible' => FALSE,
				'format' => 'date',
				'label' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:tx_media.tstamp',
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
* __buttons: display "edit", "deleted", ... buttons to control the row


Widget TCA
===========

Proposal!

It may happen that some custom content (not only field!) wants to be displayed within a form. Think that it can be some random informative text
towards the Editor for example or a custom widget which does not correspond necessarily to a field of the DB.
The "normal" way in TYPO3, would be is make a field of type "user" connected to a "userFunc" in the "column" part of the TCA. However, in some cases,
the field does not exist in the DB and inventing ghost field for that purpose sounds very hacky.

A possible marker ``widget`` could be introduced. The marker will follow the --div-- marker and would be followed by the class of a renderable widget. Example::

	--widget--;TYPO3\CMS\Media\Form\FileUpload

Where "FileUpload" implements the rendering interface. If one put this example in its context::

	$TCA['sys_file']['types]['image'] => array('showitem' => '--widget--;TYPO3\CMS\Media\Form\FileUpload ,name, title, description, alternative, caption, keywords')


Access key
=================

In a web browser, an `access key`_ allows a computer user immediately to jump to a specific part of a web page via the keyboard.

* "n" for creating a new media
* "escape" for closing the editing panel
* "s" for saving the form

.. _access key: http://en.wikipedia.org/wiki/Access_key

Todo
=================

* "?" to dipslay the access keys summary
* change icon to use TYPO3 sprite. Current icon set is the one from Twitter Bootstrap (http://twitter.github.com/bootstrap/base-css.html#icons).
* Implement action "duplicate media" in the BE module.
* Make file upload field name configurable. For now value "qqfile" is hardcoded.

Duplicate code for file:ListRow.js
--------------------------------------
<f:link.action action="duplicate" arguments="{media : media.uid}"
class="btn btn-grid btn-duplicate disabled" additionalAttributes="{data-uid: '{media.uid}'}"><i class="icon-tags"></i></f:link.action>
