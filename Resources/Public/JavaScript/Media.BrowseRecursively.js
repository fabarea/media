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
		if (sessionStorage.getItem('hasRecursiveSelection') === 'true') {
			$('#checkbox-hasRecursiveSelection').click();
			$('#ajax-additional-parameters').val('hasRecursiveSelection=1');
		}

		/**
		 * Create relation action.
		 */
		$('#checkbox-hasRecursiveSelection').change(function(e) {
			e.preventDefault();

			// Set state in session so that next time the value will be restored.
			sessionStorage.setItem('hasRecursiveSelection', $(this).is(':checked'));

			var value = 0;
			if ($(this).is(':checked')) {
				value = 1;
			}
			$('#ajax-additional-parameters').val('hasRecursiveSelection=' + value);
			Vidi.grid.fnDraw();
		});
	}
};
