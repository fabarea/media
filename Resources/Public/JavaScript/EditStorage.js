// jshint ;_;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: Fab/Media/EditStorage
 */
define([
	'jquery',
	'TYPO3/CMS/Backend/Modal'
], function($, Modal) {

	'use strict';

	/**
	 * Create relation action.
	 */
	$('.change-storage').click(function(e) {
		e.preventDefault();

		var url = EditStorage.getEditStorageUrl(this.href);

		Vidi.modal = Modal.loadUrl(
			TYPO3.l10n.localize('action.move'),
			top.TYPO3.Severity.info,
			[
				{
					text: TYPO3.l10n.localize('cancel'),
					btnClass: 'btn btn-default',
					trigger: function() {
						Modal.dismiss();
					}
				}, {
					text: TYPO3.l10n.localize('action.move'),
					btnClass: 'btn btn-default btn-change-storage',
					trigger: function() {
						$('#form-change-storage', Vidi.modal).submit();
					}
				}
			],
			url,
			function() { // callback

				// Update modal title
				var numberOfObjects = $('#numberOfObjects', Vidi.modal).html();

				var modalTitle = $('.modal-title', Vidi.modal).html() + ' - ' + numberOfObjects + ' ';
				if (numberOfObjects > 1) {
					modalTitle += TYPO3.l10n.localize('records');
				} else {
					modalTitle += TYPO3.l10n.localize('record');
				}
				$('.modal-title', Vidi.modal).html(modalTitle);

				// bind submit handler to form.
				$('#form-change-storage', Vidi.modal).on('submit', function(e) {

					// Prevent native submit
					e.preventDefault();

					$.ajax({
						url: $('#form-change-storage', Vidi.modal).attr('action'),
						data: $('#form-change-storage', Vidi.modal).serialize(),
						beforeSend: function(arr, $form, options) {

							// Only submit if button is not disabled
							if ($('.btn-change-storage', Vidi.modal).hasClass('disabled')) {
								return false;
							}

							// Else submit form
							$('.btn-change-storage', Vidi.modal).addClass('disabled');
						},
						/**
						 * On success call back
						 *
						 * @param response
						 */
						success: function(response) {

							// Hide the modal window
							Modal.dismiss();

							Vidi.Response.processResponse(response, 'update');
						}
					});

				});
			}
		);

	});


	var EditStorage = {
		/**
		 * Get edit storage URL.
		 *
		 * @param {string} url
		 * @return string
		 * @private
		 */
		getEditStorageUrl: function(url) {

			var uri = new Uri(url);
			var parametersToKeep = ['iSortCol_0', 'sSortDir_0'];

			if (Vidi.Grid.hasSelectedRows()) {
				// Case 1: mass editing for selected rows.

				// Add parameters to the Uri object.
				uri.addQueryParam(Media.parameterPrefix + '[matches][uid]', Vidi.Grid.getSelectedIdentifiers().join(','));

			} else {

				// Case 2: mass editing for all rows.
				parametersToKeep.push('sSearch');
			}

			// Keep only certain parameters which make sense to transmit.
			for (var index in Vidi.Grid.getStoredParameters()) {
				var parameter = Vidi.Grid.getStoredParameters()[index];

				// Keep only certain parameters which make sense to transmit.
				if ($.inArray(parameter.name, parametersToKeep) > -1) {
					uri.addQueryParam(parameter.name, parameter.value);
				}
			}

			return uri.toString();
		},

		/**
		 * Load content by ajax.
		 *
		 * @param {string} url
		 * @return Media.EditStorage
		 */
		loadContent: function(url) {
			// Load content by ajax for the modal window.
			$.ajax(
				{
					url: url
				})
				.done(function(data) {

					$('.modal-body').html(data);

					// bind submit handler to form.
					$('#form-change-storage').on('submit', function(e) {

						// Prevent native submit
						e.preventDefault();

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
							success: function(response) {

								// Hide modal.
								bootbox.hideAll();

								Vidi.Response.processResponse(response, 'update');
							}
						});
					});

				})
				.fail(function(data) {
					alert('Something went wrong! Check out console log for more detail');
					console.log(data);
				});


			return Media.EditStorage;
		}

	};

	return EditStorage;
});
