/**
 * Initialize Media module
 */
(function($) {
	$(function() {
		"use strict";

		Media.EditStorage.attachHandler();
		Media.BrowseRecursively.attachHandler();
	});
})(jQuery);
