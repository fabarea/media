// jshint ;_;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: Fab/Media/PluginLinkCreator
 */
define([
	'jquery',
	'Fab/Media/Media'
], function($, Media) {


	/**
	 * Handler related to the "editor image" buttons in the grid.
	 */
	$(document).on('click', '.dataTable tbody .btn-imageEditor', function(e) {
		Media.handleForm($(this).attr('href'));
		e.preventDefault();
	});

	/**
	 * Handler related to the "preview image" anchor in the grid.
	 */
	$(document).on('click', '.dataTable tbody .preview a', function(e) {
		Media.handleForm($(this).attr('href'));
		e.preventDefault();
	});

	/**
	 * True means an image has been selected in in the RTE and image editor must be loaded
	 */
	if (window.opener && window.opener.Media.ImageEditor.elementNode) {
		var $element, titleAttribute, matches, uriTarget,
			uri, classAttribute, targetAttribute, fileUid;

		$element = $(window.opener.Media.ImageEditor.elementNode);
		fileUid = $($element).data('htmlarea-file-uid');

		// Makes sure the file uid exists
		if (fileUid > 0 && $($element).data('htmlarea-file-table') === 'sys_file') {

			uriTarget = new Uri($('#btn-imageEditor-current').attr('href'));
			uriTarget.addQueryParam('tx_media_user_mediam1[file]', fileUid);

			// Setting a hidden URL with new attributes (an image was selected in the RTE)
			// and then fire click event to load Image Editor.
			$('#btn-imageEditor-current')
				.attr('href', uriTarget.query())
				.click(function(e) {
					e.preventDefault();

					// Display the form in the appropriate panel.
					Vidi.Panel.showForm();

					var url = $.ajax({
						url: $(this).attr('href'),
						success: function(data) {

							Media.setContent(data);

							// Set back values
							$('#file-title').val($($element).attr('title'));
							$('#file-class').val($($element).attr('class'));
							$('#file-target').val($($element).attr('target'));
							$('#height').val($($element).attr('height'));

							$('#width').val($($element).attr('width'))
							$('#slider').val($($element).attr('width'))

							$('.processed-file').attr({
								width: $('#width').val(),
								height: $('#height').val()
							});
						}
					});
				})
				.click(); // Fire a click
		}
	}
});
