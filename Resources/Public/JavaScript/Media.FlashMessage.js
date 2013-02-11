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
	 * @param {mixed} message
	 * @param {string} severity
	 */
	add: function (message, severity) {
		if (typeof severity == 'undefined') {
			severity = 'success';
		}
		this.stack.push({"message": message, "severity": severity});
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
	showAll: function () {
		var flashMessage, message, output, index;

		// Clear stack first
		$(".flash-message").html('');

		while (flashMessage = this.pop()) {
			this.show(flashMessage['message'], flashMessage['severity']);
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
		$(".flash-message").append($(output)).css("margin-left", positionWidthCss);
		$(".alert").delay(2000).fadeOut("slow", function () {
			$(this).remove();
		});
	}
};

