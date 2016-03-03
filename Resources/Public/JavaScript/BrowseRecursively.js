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
 * Module: Fab/Media/BrowseRecursively
 */
define([
	'jquery'
], function($) {

	/**
	 * Click in order to mark / un-mark the checkbox before attaching the event.
	 */
	if (sessionStorage.getItem('hasRecursiveSelection') === 'false') {
		$('#checkbox-hasRecursiveSelection').click();
		$('#ajax-additional-parameters').val('hasRecursiveSelection=0');
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
	'use strict';
});
