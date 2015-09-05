/** @namespace Media */

/**
 * Object for handling "edit-storage" actions.
 *
 * @type {Object} Media.EditStorage
 */
Media.EditStorage = {

	/**
	 * Bind edit storage in the menu action.
	 *
	 * @return void
	 */
	attachHandler: function() {
		/**
		 * Create relation action.
		 */
		$('.change-storage').click(function(e) {
			e.preventDefault();

			var url = Media.EditStorage.getEditStorageUrl(this.href);

			// Call the Edit Storage routine which will pop-up the modal window.
			Media.EditStorage
				.loadContent(url)
				.showWindow();

		});
	},

	/**
	 * Get edit storage URL.
	 *
	 * @param {string} url
	 * @return string
	 * @private
	 */
	getEditStorageUrl: function (url) {

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
	loadContent: function (url) {
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
	},

	/**
	 * Show the popup window.
	 *
	 * @return void
	 */
	showWindow: function () {

		// Display the empty modal box with default loading icon.
		// Its content is going to be replaced by the content of the Ajax request.
		var template = '<div style="text-align: center">' +
			'<img src="' + Vidi.module.publicPath + 'Resources/Public/Images/loading.gif" width="" height="" alt="" />' +
			'</div>';

		var modalWindowConfiguration = [
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
		];

		bootbox.dialog(
			template,
			modalWindowConfiguration, {
				onEscape: function () {
					// Empty but required function to have escape keystroke hiding the modal window.
				}
			});
	}

};
