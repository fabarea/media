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
	showForm: function () {

		// Store the loading message
		if (typeof Media.icons.loading == 'undefined') {
			Media.icons.loading = $('#container-main-sub').html();
		}
		this.togglePanel();
	},

	/**
	 * Display THE "list" panel
	 *
	 * @return void
	 */
	showList: function () {

		// Remove footer and header markup.
		$('#footer > *').remove();
		$('#navbar-sub > *').remove();


		// Add loading message for the next time the panel is displayed
		$('#container-main-sub').html(Media.icons.loading);
		this.togglePanel();

		Media.Table.fnDraw();
	},

	/**
	 * Toggle visibility of various panels
	 *
	 * @private
	 * @return void
	 */
	togglePanel: function () {
		// Expand / Collapse widgets
		$(['container-main-top', 'container-main-sub', 'navbar-main', 'navbar-sub']).each(function (index, value) {
			$('#' + value).toggle();
		});

	}
};
