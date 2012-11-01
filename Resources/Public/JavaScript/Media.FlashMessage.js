"use strict";

/** @namespace Media */

/**
 * Object for handling flash messages
 *
 * @type {Object} FlashMessage
 */
Media.FlashMessage = {

	/**
	 * The stack containing the message
	 */
	stack: [],

	/**
	 * Stack
	 *
	 * @param {mixed} data
	 * @param {string} key
     * @param {string} severity
	 */
	add: function(data, key, severity) {
		if (typeof severity == 'undefined') {
			severity = 'success';
		}
		this.stack.push({"data": data, "key": key, "severity": severity});
	},

	/**
	 * Returns the last element of the stack and pops it out
	 *
	 * @return {Object}
	 */
	pop: function () {
		return this.stack.pop();
	},

	/**
	 * Display all message from the stack
	 *
	 * @return void
	 */
	display: function () {
		var message, data, output, index;

		while(message = this.pop()) {
			data = $.parseJSON(message["data"]);
			if (data.status) {
				output = Media.format(message["key"], data.media.uid);
				if (data.media.title) {
					output = Media.format(message["key"], data.media.title);
				}
				this.show('<strong>' + output + '</strong>', message["severity"]);
			} else {
				output = Media.translate('message-error')
				this.show('<strong>' + output + '</strong>', 'error');
			}
		}
	},

	/**
	 * Pop-up a flash message
	 *
	 * @param {string} message
	 * @param {string} severity
	 */
	show: function (message, severity) {

		var positionWidthCss, width, output;

		// Compute positioning of the flash message box
		width = $('.flash-message').outerWidth();
		positionWidthCss = '-' + width / 2 + 'px';

		// Prepare output
		output = '<div class="alert alert-' + severity + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>';

		// Manipulate DOM to display flash message
		$(".flash-message").html($(output)).css("margin-left", positionWidthCss);
		$(".alert").delay(2000).fadeOut("slow", function () {
			$(this).remove();
		});
	}
}
