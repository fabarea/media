"use strict";

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
			Media.icons.loading = $('#accordion-inner-bottom').html();
		}

		// Show form panel
		$('#collapseTop').collapse('toggle');
		$('#collapseBottom').collapse('toggle')
	},

	/**
	 * Display THE "list" panel
	 *
	 * @return void
	 */
	showList: function () {

		$('#form-media').html(Media.icons.loading);

		// Show form panel
		$('#collapseTop').collapse('toggle');
		$('#collapseBottom').collapse('toggle')
		Media.Table.fnDraw();
	}
}