"use strict";

/** @namespace Media */
/**
 * Object for handling event and their actions
 *
 * @type {Object} Event
 */
Media.Action = {

	/**
	 * Bind add button in the nav bar.
	 *
	 * @return void
	 */
	add: function () {
		$('.btn-new').click(function (e) {
			e.preventDefault();

			Media.Panel.showForm();
			Media.Action.handleForm($(this).attr('href'), 'message-created');
		});
	},

	/**
	 * Bind edit buttons in list view.
	 *
	 * @return void
	 */
	edit: function () {

		// bind the click handler script to the newly created elements held in the table
		$('.btn-edit').bind('click', function (e) {
			e.preventDefault();

			Media.Panel.showForm();
			Media.Action.handleForm($(this).attr('href'), 'message-updated');

		});
	},

	/**
	 * Bind delete buttons in list view.
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
	},

	/**
	 * Fetch the form and handle its action
	 *
	 * @private
	 * @param {string} url where to send the form data
	 * @param {string} key corresponds to an identifier for the flash message queue.
	 * @return void
	 */
	handleForm: function (url, key) {
		// Send Ajax request to delete media
		$.ajax({
			url: url,
			success: function (data) {

				Media.Action.setContent(data);
				Media.parseScript(Media.getBodyContent(data));

				// Restore GUI.
				Media.Session.initialize();

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
							Media.FlashMessage.add(data, key, 'success');
						}
					})
				});
			}
		});
	},

	/**
	 * Update the content on the GUI.
	 *
	 * @private
	 * @param {string} data
	 * @return void
	 */
	setContent: function (data) {

		// replace content
		var content;
		$.each(['header', 'body', 'footer'], function (index, value) {
			// @bug filter() only find the first element after tag body...
			//var content = $(data).filter('#content-middle');

			// find method will remove the outer tag
			content = $(data).find('#content-' + value).html();


			if (content.length > 0) {
				$('.ajax-response-' + value).html(content);
			}
		});
	}
};

