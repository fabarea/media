"use strict"; // jshint ;_;

/** @namespace Media */

/**
 * Object for handling panels
 *
 * @type {Object} Panel
 */
Media.Panel = {

	/**
	 * Display THE "form" panel
	 *
	 * @return void
	 */
	showForm: function() {

		// Store the loading message
		if (typeof Media.icons.loading == 'undefined') {
			Media.icons.loading = $('#container-bottom').html();
		}
		this.togglePanel();
	},

	/**
	 * Display THE "list" panel
	 *
	 * @return void
	 */
	showList: function() {

		// Add loading message for the next time the frame is displayed
		$('#container-bottom').html(Media.icons.loading);
		this.togglePanel();

		Media.Table.fnDraw();
	},

	/**
	 * Toggle visibility of various panels
	 *
	 * @private
	 * @return void
	 */
	togglePanel: function() {
		// Expand / Collapse widgets
		$(['container-top', 'container-bottom', 'navbar-media-default', 'navbar-media-save']).each(function (index, value) {
			$('#' + value).toggle();
		});
	}
}