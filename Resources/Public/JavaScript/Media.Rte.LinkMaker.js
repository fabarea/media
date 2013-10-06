/*
 * Media link creator
 *
 * Load the proper panel whenever a link is selected in the editor.
 * Info: window.opener is the variable for exchanging data with the parent window
 */
(function($) {
	$(function() {

		/**
		 * Bind handler against RTE link maker buttons in the grid.
		 */
		$(document).on('click', '.dataTable tbody .btn-linkMaker', function (e) {
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

		// True means a link already exist in the RTE and must be updated.
		if (window.opener && window.opener.Media.LinkMaker.elementNode) {
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

				// Fire a click
				$('#btn-linkMaker-current').click();
			}
		}
	});
})(jQuery);
