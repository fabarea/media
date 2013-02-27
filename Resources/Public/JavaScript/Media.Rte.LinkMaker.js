/*
 * Media link creator
 */
(function($) {
	$(function() {

		/**
		 * Load the proper panel whenever an image is selected in the editor.
		 * Info: window.opener is the variable for exchanging data with the parent window
		 */
		// True means a link already exist in the RTE and must be updated.
		if (window.opener.Media.LinkMaker.elementNode) {
			var $element, titleAttribute, matches,
				uri, classAttribute, targetAttribute, fileUid;

			$element = $(window.opener.Media.LinkMaker.elementNode);

			uri = new Uri($($element).attr('href'))
			matches = uri.query().match(/file:([0-9]+)/i);
			if (matches.length > 0) {
				fileUid = matches[1];

				var uriTarget = new Uri($('#btn-linkMaker-current').attr('href'));
				uriTarget.addQueryParam('tx_media_user_mediam1[asset]', fileUid);

				// Reset the URL with the new attribute
				$('#btn-linkMaker-current').attr('href', uriTarget.query());
				$('#btn-linkMaker-current').click(function(e) {
					e.preventDefault();

					// Display the form in the appropriate panel.
					Media.Panel.showForm();

					var url = $.ajax({
						url: $(this).attr('href'),
						success: function (data) {

							Media.Action.setContent(data);
							Media.parseScript(Media.getBodyContent(data));

							// Set back values
							$('#file-title').val($($element).attr('title'));
							$('#file-class').val($($element).attr('class'));
							$('#file-target').val($($element).attr('target'));
						}
					});
				})

				// Fire a click
				$('#btn-linkMaker-current').click();
			}
		}
	});
})(jQuery);
