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
			$('#ajax-additional-parameters').val('hasRecursiveBrowsing=true');
		}

		/**
		 * Create relation action.
		 */
		$('#checkbox-hasRecursiveBrowsing').change(function(e) {
			e.preventDefault();

			sessionStorage.setItem('hasRecursiveBrowsing', $(this).is(':checked'));

			$('#ajax-additional-parameters').val('hasRecursiveBrowsing=' + $(this).is(':checked'));
			Vidi.grid.fnDraw();
		});
	}
};
