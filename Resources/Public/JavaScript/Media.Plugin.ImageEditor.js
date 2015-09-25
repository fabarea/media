/*
 * Media link creator
 *
 * Load the proper panel whenever an image is selected in the editor.
 * Info: window.opener is the variable for exchanging data with parent window
 */
(function ($) {
	$(function () {

		/**
		 * Handler related to the "editor image" buttons in the grid.
		 */
		$(document).on('click', '.dataTable tbody .btn-imageEditor', function (e) {
			Media.handleForm($(this).attr('href'));
			e.preventDefault();
		});

		/**
		 * Handler related to the "preview image" anchor in the grid.
		 */
		$(document).on('click', '.dataTable tbody .preview a', function (e) {
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
					.click(function (e) {
						e.preventDefault();

						// Display the form in the appropriate panel.
						Vidi.Panel.showForm();

						var url = $.ajax({
							url: $(this).attr('href'),
							success: function (data) {

								Media.setContent(data);

								// Set back values
								$('#file-title').val($($element).attr('title'));
								$('#file-class').val($($element).attr('class'));
								$('#file-target').val($($element).attr('target'));
								$('#height').val($($element).attr('height'));
								$('#width')
									.val($($element).attr('width'))
									.change(); // Fire up change event to adjust the GUI.
							}
						});
					})
					.click(); // Fire a click
			}
		}
	});
})(jQuery);

/** @namespace Media */
/**
 * Object for handling event and their actions
 *
 * @type {Object} Event
 */
Media.Rte = {
	Popup: {

		/**
		 * Create image link in the RTE.
		 *
		 * @private
		 * @param {object} data
		 * @return void
		 */
		createImage: function (data) {

			var params = {};
			var hostAndProtocol = location.protocol + '//' + location.host + '/';
			params.tag = '<img src="' + hostAndProtocol + data.publicUrl +
				'" title="' + data.title +
				'" alt="' + data.title +
				'" height="' + data.height +
				'" width="' + data.width +
				'" data-htmlarea-file-uid="' + data.original +
				'" data-htmlarea-file-table="sys_file" ';

			if (window.opener) {
				// Reset previous class names applied against the image tag.
				if (window.opener.Media.ImageEditor.elementNode.className != null) {
					params.tag += 'class="' + window.opener.Media.ImageEditor.elementNode.className + '" ';
				}

				// Reset previous style applied against the image tag.
				if (window.opener.Media.ImageEditor.elementNode.style != null &&
					window.opener.Media.ImageEditor.elementNode.style.cssText != null) {
					params.tag += 'style="' + window.opener.Media.ImageEditor.elementNode.style.cssText + '" ';
				}
				params.tag += '/>';

				// write the "img" tag in the RTE
				window.opener.Media.ImageEditor.insertImage(params);
				window.close();
			}
		}
	}
};