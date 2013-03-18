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
	 * @todo remove me in Media 1.1
	 *
	 * @return void
	 */
	add: function () {
		$('.btn-new').click(function (e) {
			e.preventDefault();

			Media.Panel.showForm();
			Media.Action.handleFormWithMessage($(this).attr('href'), 'message-created');
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
			Media.Action.handleFormWithMessage($(this).attr('href'), 'message-updated');

		});
	},

	/**
	 * Bind RTE link maker buttons in list view.
	 *
	 * @return void
	 */
	linkMaker: function () {
		// bind the click handler script to the newly created elements held in the table
		$('.btn-linkMaker')
			.bind('click', Media.Action.callForm)
			.each(function (index) {
				// find the anchor object of the thumbnail and attach call form action as well
				$(this)
					.closest('tr')
					.find('a')
					.first()
					.attr('href', $(this).attr('href'))
					.bind('click', Media.Action.callForm);
			});
	},

	/**
	 * Bind RTE image maker buttons in list view.
	 *
	 * @return void
	 */
	imageMaker: function () {
		// bind the click handler script to the newly created elements held in the table
		$('.btn-imageMaker')
			.bind('click', Media.Action.callForm)
			.each(function (index) {
				// find the anchor object of the thumbnail and attach call form action as well
				$(this)
					.closest('tr')
					.find('a')
					.first()
					.attr('href', $(this).attr('href'))
					.bind('click', Media.Action.callForm);
			});
	},

	/**
	 * Calls editing form
	 *
	 * @private
	 * @param {Object} e
	 * @return void
	 */
	callForm: function (e) {
		e.preventDefault();
		Media.Panel.showForm();
		Media.Action.handleForm($(this).attr('href'));
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
			message = Media.format("confirm-message", $.trim(title));

			bootbox.confirm(message, function (result) {
				if (result) {

					// Send Ajax request to delete media
					$.get(url,
						function (data) {
							// Reload data table
							Media.Table.fnDeleteRow(Media.Table.fnGetPosition(row));
							var message = Media.format('message-deleted', data.asset.title);
							Media.FlashMessage.add(message, 'success');
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
	 * @return void
	 */
	handleForm: function (url) {
		$.ajax({
			url: url,
			success: function (data) {
				Media.Action.setContent(data);
			}
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
	handleFormWithMessage: function (url, key) {
		// Send Ajax request to delete media
		$.ajax({
			url: url,
			success: function (data) {

				Media.Action.setContent(data);

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
							var mediaTitle, message;
							mediaTitle = data.asset.title == '' ? data.asset.uid : data.asset.title;
							message = Media.format(key, mediaTitle);
							Media.FlashMessage.add(message, 'success');
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

