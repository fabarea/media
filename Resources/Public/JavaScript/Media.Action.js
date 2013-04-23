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
			Media.Session.set('media.lastEditedUid', $(this).data('uid'));
		});

		// Make a row selectable
		$('.checkbox-row').bind('click', function (e) {
			var checkboxes;

			$(this)
				.closest('tr')
				.toggleClass('active');
			e.stopPropagation(); // we don't want the event to propagate.

			checkboxes = $('#media-list').find('.checkbox-row').filter(':checked');
			if (checkboxes.length > 0) {
				$('.mass-action').removeClass('disabled');
			} else {
				$('.mass-action').addClass('disabled');
			}
		});

		// Add listener on the row as well
		$('.checkbox-row')
			.parent()
			.css('cursor', 'pointer')
			.bind('click', function (e) {
				$(this).find('.checkbox-row').click()
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
		$('.btn-delete')
			.click(function () {
				Media.Action.scope = this;
			})
			// Click-over is a jQuery Plugin extending pop-over plugin from Twitter Bootstrap
			.clickover({
				esc_close: true,
				width: 200,
				placement: 'left',
				title: Media.translate('are-you-sure'),
				content: "<div class='btn-toolbar'>" +
					"<button data-dismiss='clickover' class='btn'>Cancel</button>" +
					"<button class='btn btn-danger btn-delete-row'>Delete</button>" +
					"</div>",
				onShown: function () {

					// Element corresponds to the click-over box. Keep it accessible in the closure.
					var element = this;

					// bind click on "btn-delete-row"
					$('.btn-delete-row').bind('click', function (e) {
						var row, title, message, url;

						$(this).addClass('disabled').text(Media.translate('deleting'));
						url = $(Media.Action.scope).attr('href');

						// Compute media title
						row = $(Media.Action.scope).closest("tr").get(0);
						title = $('.media-title', row).html();
						message = Media.format("confirm-delete", $.trim(title));

						// Send Ajax request to delete media
						$.get(url,
							function (data) {

								// Hide click-over box.
								element.hide();

								// Reload data table
								Media.table.fnDeleteRow(Media.table.fnGetPosition(row));
								var message = Media.format('message-deleted', data.asset.title);
								Media.FlashMessage.add(message, 'success');
							}
						);
					});
				}
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

