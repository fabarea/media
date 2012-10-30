"use strict";

/** @namespace Media */

/**
 * Flash message handling
 *
 * @param {string} message
 * @param {string} severity
 */
Media.flashMessage = function (message, severity) {
	var positionWidthCss, width, output;

	// Compute positioning of the flash message box
	width = $('.flash-message').outerWidth();
	positionWidthCss = '-' + width / 2 + 'px';

	// Prepare output
	output = '<div class="alert alert-' + severity + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>';

	// Manipulate DOM to display flash message
	$(".flash-message").html($(output)).css("margin-left", positionWidthCss);
	$(".alert").delay(2000).fadeOut("slow", function () {
		$(this).remove();
	});
}

$(document).ready(function () {

	/**
	 * Table initialisation
	 *
	 * Internal note: properties of Datatables have prefix: m, b, s, i, o, a, fn etc...
	 * this corresponds to the variable type e.g. mixed, boolean, string, integer, object, array, function
	 */
	Media.Table = $('#example').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/typo3/mod.php",
		"fnServerParams": function (aoData) {
			aoData.push({ "name": "M", "value": "user_MediaTxMediaM1" });
			aoData.push({ "name": "tx_media_user_mediatxmediam1[action]", "value": "listRow" });
			aoData.push({ "name": "tx_media_user_mediatxmediam1[controller]", "value": "Media" });
			aoData.push({ "name": "tx_media_user_mediatxmediam1[format]", "value": "json" });
		},
		"oTableTools": {
			"sRowSelect": "multi",
			"fnRowSelected": function (node) {
				console.log(node.id);
				console.log(node);
			}
		},
		"fnDrawCallback": function () {

			//bind the click handler script to the newly created elements held in the table
			$('.btn-delete').bind('click', function (e) {

				var row, title, message, url;

				url = $(this).attr('href');
				// compute media title
				row = $(this).closest("tr").get(0);
				title = $('.media-title', row).html();
				message = Media.Language["confirm-message"].format(title);

				bootbox.confirm(message, function (result) {
					if (result) {

						// Send Ajax request to delete media
						$.get(url,
							function (data) {
								var message;
								data = $.parseJSON(data);
								if (data.status) {

									Media.Table.fnDeleteRow(Media.Table.fnGetPosition(row));

									message = Media.Language["message-deleted"].format(data.media.uid);
									if (data.media.title) {
										message = Media.Language["message-deleted"].format(data.media.title);
									}
									Media.flashMessage('<strong>' + message + '</strong>', 'success');
								}
							}
						);
					}
				});
				e.preventDefault();
			});
		}
	});

	// Confirmation window

});


String.prototype.format = String.prototype.f = function () {
	var s = this,
		i = arguments.length;

	while (i--) {
		s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i]);
	}
	return s;
};