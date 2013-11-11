/*
 * Media link creator
 *
 * Load the proper panel whenever an image is selected in the editor.
 * Info: window.opener is the variable for exchanging data with parent window
 */
(function ($) {
	$(function () {

		/**
		 * Bind handler against RTE image editor buttons in the grid.
		 */
		$(document).on('click', '.dataTable tbody .btn-imageEditor', function (e) {
			Media.handleForm($(this).attr('href'));
			e.preventDefault();
		});

		/**
		 * Bind handler against image preview buttons in the grid.
		 */
		$(document).on('click', '.dataTable tbody .preview a', function (e) {
			Media.handleForm($(this).attr('href'));
			e.preventDefault();
		});

		/**
		 * Handler against Variant icon.
		 */
		$(document).on('click', '.dataTable tbody .btn-variant-link', function (e) {
			e.preventDefault();
			var data = {
				uid: $(this).data('file-uid'),
				original: $(this).data('original-uid'),
				publicUrl: $(this).data('public-url'),
				timeStamp: $(this).data('time-stamp')
			};
			Media.Rte.Popup.createImage(data);
		});

		// True means a link already exist in the RTE and must be updated.
		if (window.opener && window.opener.Media.ImageEditor.elementNode) {
			var $element, titleAttribute, matches,
				uri, classAttribute, targetAttribute,
				variantUid, originalUid;

			$element = $(window.opener.Media.ImageEditor.elementNode);

			variantUid = $($element).data('htmlarea-file-uid');
			originalUid = $($element).data('htmlarea-original-uid');

			// Makes sure the variant uid exists
			if (variantUid > 0 && $($element).data('htmlarea-file-table') == 'sys_file') {

				var uriTarget = new Uri($('#btn-imageEditor-current').attr('href'));
				if (originalUid > 0) {
					uriTarget.addQueryParam('tx_media_user_mediam1[asset]', originalUid);
					uriTarget.addQueryParam('tx_media_user_mediam1[variant]', variantUid);
				} else {
					// In case we can not find a
					uriTarget.addQueryParam('tx_media_user_mediam1[asset]', variantUid);
				}

				// Resetting a hidden URL with new attributes (an image was selected in the RTE)
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
		 * Create image link.
		 *
		 * @private
		 * @param {object} element
		 * @return void
		 */
		createImage: function (data) {

			var params = {};
			var hostAndProtocol = location.protocol + '//' + location.host + '/';
			params.tag = '<img src="' + hostAndProtocol + data.publicUrl + '?' + data.timeStamp +
				'" data-htmlarea-original-uid="' + data.original +
				'" data-htmlarea-file-uid="' + data.uid +
				'" data-htmlarea-file-table="sys_file" ';

			if (window.opener) {
				// apply previous classes
				if (window.opener.Media.ImageEditor.elementNode.className != null) {
					params.tag += 'class="' + window.opener.Media.ImageEditor.elementNode.className + '" '
				}

				// apply previous styles
				if (window.opener.Media.ImageEditor.elementNode.style != null &&
					window.opener.Media.ImageEditor.elementNode.style.cssText != null) {
					params.tag += 'style="' + window.opener.Media.ImageEditor.elementNode.style.cssText + '" '
				}
				params.tag += '/>';

				// write the "img" tag in the RTE
				window.opener.Media.ImageEditor.insertImage(params);
				window.close();
			}

		}
	}
};