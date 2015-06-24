/** @namespace Media */

/**
 * Object for handling "edit-storage" actions.
 *
 * @type {Object} Media.EditStorage
 */
Media.BrowseRecursively = {

	/**
	 * Bind edit storage in the menu action.
	 *
	 * @return void
	 */
	attachHandler: function() {

		/**
		 * Click in order to mark / un-mark the checkbox before attaching the event.
		 */
		if (sessionStorage.getItem('hasRecursiveBrowsing') === 'true') {
			$('#checkbox-hasRecursiveBrowsing').click();
			$('#ajax-additional-parameters').val('hasRecursiveBrowsing=1');
		}

		/**
		 * Create relation action.
		 */
		$('#checkbox-hasRecursiveBrowsing').change(function(e) {
			e.preventDefault();

			// Set state in session so that next time the value will be restored.
			sessionStorage.setItem('hasRecursiveBrowsing', $(this).is(':checked'));

			var value = 0;
			if ($(this).is(':checked')) {
				value = 1;
			}
			$('#ajax-additional-parameters').val('hasRecursiveBrowsing=' + value);
			Vidi.grid.fnDraw();
		});
	}
};
