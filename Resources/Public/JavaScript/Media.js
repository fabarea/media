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
 * Module: Fab/Media/Media
 */
define([
	'jquery',
	'Fab/Vidi/Vidi/Panel'
], function($, Panel) {

	'use strict';

	var Media = {

		/**
		 * Fetch the form and handle its action
		 *
		 * @param {string} url where to send the form data
		 * @return void
		 */
		handleForm: function(url) {
			Panel.showForm();
			$.ajax({
				url: url,
				success: function(data) {
					Media.setContent(data);
				}
			});
		},

		/**
		 * Update the content on the GUI.
		 *
		 * @param {string} data
		 * @return void
		 */
		setContent: function(data) {

			// replace content
			var content;
			$.each(['header', 'body', 'footer'], function(index, value) {
				// @bug filter() only find the first element after tag body...
				//var content = $(data).filter('#content-middle');

				// find method will remove the outer tag
				content = $(data).find('#content-' + value).html();

				if (content.length > 0) {
					$('.ajax-response-' + value).html(content);
				}
			});
		}
	};

	return Media;
});
