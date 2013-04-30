/*
 * Media link creator
 */
(function ($) {
	$(function () {

		/**
		 * Load the proper panel whenever an image is selected in the editor.
		 * Info: window.opener is the variable for exchanging data with parent window
		 */

		// True means a link already exist in the RTE and must be updated.
		if (window.opener && window.opener.Media.ImageMaker.elementNode) {
			var $element, titleAttribute, matches,
				uri, classAttribute, targetAttribute,
				variantUid, originalUid;

			$element = $(window.opener.Media.ImageMaker.elementNode);

			variantUid = $($element).data('htmlarea-file-uid');
			originalUid = $($element).data('htmlarea-original-uid');

			// Makes sure the variant uid exists
			if (variantUid > 0 && $($element).data('htmlarea-file-table') == 'sys_file') {

				var uriTarget = new Uri($('#btn-imageMaker-current').attr('href'));
				if (originalUid > 0) {
					uriTarget.addQueryParam('tx_media_user_mediam1[asset]', originalUid);
					uriTarget.addQueryParam('tx_media_user_mediam1[variant]', variantUid);
				} else {
					// In case we can not find a
					uriTarget.addQueryParam('tx_media_user_mediam1[asset]', variantUid);
				}

				// Resetting a hidden URL with new attributes (an image was selected in the RTE)
				// and then fire click event to load Image Editor.
				$('#btn-imageMaker-current')
					.attr('href', uriTarget.query())
					.click(function (e) {
						e.preventDefault();

						// Display the form in the appropriate panel.
						Media.Panel.showForm();

						var url = $.ajax({
							url: $(this).attr('href'),
							success: function (data) {

								Media.Action.setContent(data);

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
