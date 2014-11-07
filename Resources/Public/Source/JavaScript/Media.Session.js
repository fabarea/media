/** @namespace Media */

/**
 * Object for handling session
 *
 * @type {Object} Event
 */
Media.Session = {

	/**
	 * Initialize the session with default value if not set.
	 *
	 * @return void
	 */
	initialize: function () {

		// @todo remove me if not used in a close future e.g. for mass-editing!
		// Bind action when a tab is selected
		$('.nav-tabs li a').bind('click', function (e) {
			var selectedTab = $(this).parent();
			var selectedIndex = $('.nav-tabs li').index(selectedTab);
			sessionStorage.setItem('media.selectedTab', selectedIndex);
		});

		// Initialize default value
		if (window.sessionStorage) {
			if (sessionStorage.getItem('media.selectedTab') == null) {
				sessionStorage.setItem('media.selectedTab', 0);
			}
		}

		// @todo remove me if not used in a close future e.g. for mass-editing!
		// In case the form is loaded
		var selectedTab = sessionStorage.getItem('media.selectedTab');
		$('.nav-tabs li:eq(' + selectedTab + ') a').tab('show');
	},

	/**
	 * Get a key in from the session.
	 *
	 * @param {string} key corresponds to an identifier
	 * @return mixed
	 */
	get: function (key) {
		var result;
		result = null;
		if (window.sessionStorage) {
			result = sessionStorage.getItem(key);
		}
		return result;
	},

	/**
	 * Set a key in session.
	 *
	 * @param {string} key corresponds to an identifier
	 * @param {string} value
	 * @return void
	 */
	set: function (key, value) {
		if (window.sessionStorage) {
			sessionStorage.setItem(key, value);
		}
	},

	/**
	 * Reset a key from the session.
	 *
	 * @param {string} key corresponds to an identifier
	 * @return void
	 */
	reset: function (key) {
		if (window.sessionStorage) {
			sessionStorage.setItem(key, '');
		}
	},

	/**
	 * Tell whether a value exists for a key.
	 *
	 * @param {string} key corresponds to an identifier
	 * @return bool
	 */
	has: function (key) {
		return this.get(key) !== null && this.get(key) !== '';
	}
};
