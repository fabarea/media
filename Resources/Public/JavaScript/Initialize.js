/**
 * Initialize Media module
 */
(function($) {
	$(function() {
		"use strict";

		/**
		 * Create relation action.
		 */
		$('.change-storage').click(function(e) {
			var uri, storage, selectedIdentifiers, selectedIdentifier;

			// Get a possible selected storage.
			uri = new Uri(window.location.href);
			if (uri.getQueryParamValue('tx_vidi_user_vidisysfilem1[storage]')) {
				storage = uri.getQueryParamValue('tx_vidi_user_vidisysfilem1[storage]');
			}

			selectedIdentifiers = [];
			$('#content-list')
				.find('.checkbox-row')
				.filter(':checked')
				.each(function(index) {
					selectedIdentifier = $(this).data('uid');
					selectedIdentifiers.push(selectedIdentifier);
				});

			// Get content by ajax for the modal...
			$.ajax(
				{
					type: 'get',
					url: $(e.target).attr('href'),
					data: {
						'tx_media_user_mediam1[controller]': 'Storage',
						'tx_media_user_mediam1[action]': 'list',
						'tx_vidi_user_vidisysfilem1[storage]': storage
					}
				})
				.done(function(data) {

					$('.modal-body').html(data);

					// bind submit handler to form.
					$('#form-change-storage').on('submit', function(e) {

						// Prevent native submit
						e.preventDefault();

						// Inject identifiers of selected rows.
						$('#inputAssets').val(selectedIdentifiers.join(','));

						$.each(selectedIdentifiers, function(index, value) {
							$('#form-change-storage')
								.append('<input type="hidden" name="tx_media_user_mediam1[assets][]" value="{0}"/>'.format(value))
						});


						// Register
						$(this).ajaxSubmit({

							/**
							 * Before submit handler.
							 * @param arr
							 * @param $form
							 * @param options
							 * @returns {boolean}
							 */
							beforeSubmit: function(arr, $form, options) {

								// Only submit if button is not disabled
								if ($('.btn-change-storage').hasClass('disabled')) {
									return false;
								}

								// Else submit form
								$('.btn-change-storage').text('Saving...').addClass('disabled');
							},

							/**
							 * On success call back
							 * @param data
							 */
							success: function(data) {

								// Hide modal.
								bootbox.hideAll();

								$('.checkbox-row-top').removeAttr('checked'); // un-check the top checkbox.

								// Reload data table.
								Vidi.grid.fnDraw();
							}
						})
					});

				})
				.fail(function(data) {
					alert('Something went wrong! Check out console log for more detail');
					console.log(data);
				});

			// Display modal box with default loading icon.
			var template = '<div style="text-align: center">' +
				'<img src="' + Vidi.module.publicPath + 'Resources/Public/Images/loading.gif" alt="" />' +
				'</div>';

			bootbox.dialog(template, [
				{
					'label': 'Cancel'
				},
				{
					'label': 'Change storage',
					'class': 'btn-primary btn-change-storage',
					'callback': function() {

						$('#form-change-storage').submit();

						// Prevent modal closing ; modal window will be closed after submitting.
						return false;
					}
				}
			], {
				onEscape: function() {
					// required to have escape keystroke hiding modal window.
				}
			});
			e.preventDefault()
		});

		/**
		 * Mass delete action.
		 * This code was copy-pasted from Vidi.js. Check if the code can be made more generic.
		 */
		$('.mass-delete-assets').click(function(e) {
			var checkboxes, message, url, uid;

			e.preventDefault();
			url = $(this).attr('href');

			checkboxes = [];
			$('#content-list')
				.find('.checkbox-row')
				.filter(':checked')
				.each(function(index) {
					uid = $(this).data('uid');
					checkboxes.push(uid);
					// @changed from Vidi.js
					url += '&{0}[assets][{1}]={2}'.format('tx_media_user_mediam1', index, uid);
				});


			message = Vidi.format("confirm-mass-delete-plural", checkboxes.length);
			if (checkboxes.length <= 1) {
				message = Vidi.format("confirm-mass-delete-singular", checkboxes.length);
			}

			bootbox.dialog(message, [
				{
					'label': Vidi.translate('cancel')
				},
				{
					'label': Vidi.translate('delete'),
					'class': "btn-danger",
					'callback': function() {
						$.get(url,
							function(data) {
								message = Vidi.format('message-mass-deleted-plural', checkboxes.length);
								if (checkboxes.length <= 1) {
									message = Vidi.format('message-mass-deleted-singular', checkboxes.length);
								}
								Vidi.FlashMessage.add(message, 'success');
								Vidi.FlashMessage.showAll();

								$('.checkbox-row-top').removeAttr('checked'); // un-check the top checkbox.

								// Reload data table
								Vidi.grid.fnDraw();
							}
						);
					}
				}
			]);
		});
	});
})(jQuery);
