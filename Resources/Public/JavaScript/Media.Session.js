"use strict";

/** @namespace Media */

/**
 * Object for handling session
 *
 * @type {Object} Event
 */
Media.Session = {

	/**
	 * Fetch the form and handle its action
	 *
	 * @private
	 * @param {string} url where to send the form data
	 * @param {string} key corresponds to an identifier for the flash message queue.
	 * @return void
	 */
	initialize: function () {

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

		// In case the form is loaded
		var selectedTab = sessionStorage.getItem('media.selectedTab');
		$('.nav-tabs li:eq(' + selectedTab + ') a').tab('show');
	}
};
