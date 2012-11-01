"use strict";

/** @namespace Media */

/**
 * Object for handling event and their actions
 *
 * @type {Object} Event
 */
Media.Event = {

	/**
	 * Bind edit buttons in list view
	 *
	 * @return void
	 */
	edit: function () {

		//bind the click handler script to the newly created elements held in the table
		$('.btn-edit').bind('click', function (e) {
			e.preventDefault();

			Media.Panel.showForm();

			// Send Ajax request to delete media
			$.get($(this).attr('href'),
				function (data) {
					// @bug in jQuery? find() does not find anything if element searched is just after tag body. Notice also it removes the tag found
					//var content = $(data).find('form').html();

					// @bug filter() only find the first element after tag body...
					var content = $(data).filter('#form-media')
					$('#container-bottom').html(content);

					// bind submit handler to form
					$('#form-media').on('submit', function (e) {
						e.preventDefault(); // prevent native submit
						$(this).ajaxSubmit({
							beforeSubmit: function () {
								$('#form-media').css('opacity', 0.5);
							},
							success: function (data) {
								// Reload data table
								Media.Panel.showList();
								Media.FlashMessage.add(data, "message-updated", 'success');
							}
						})
					});
				}
			);
		});
	},

	/**
	 * Bind delete buttons in list view
	 *
	 * @return void
	 */
	delete: function () {

		//bind the click handler script to the newly created elements held in the table
		$('.btn-delete').bind('click', function (e) {

			var row, title, message, url;

			url = $(this).attr('href');
			// compute media title
			row = $(this).closest("tr").get(0);
			title = $('.media-title', row).html();
			message = Media.format("confirm-message", title);

			bootbox.confirm(message, function (result) {
				if (result) {

					// Send Ajax request to delete media
					$.get(url,
						function (data) {
							// Reload data table
							Media.Table.fnDeleteRow(Media.Table.fnGetPosition(row));
							Media.FlashMessage.add(data, "message-deleted", 'success');
						}
					);
				}
			});
			e.preventDefault();
		});
	}
}
