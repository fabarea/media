"use strict";

/** @namespace Media */

/**
 * Language object
 *
 * @type {Object}
 */
Media.Language = {

	/**
	 * array containing all labels
	 */
	labels: Media._labels,

	/**
	 *
	 * @param key
	 * @return string
	 */
	get: function (key) {
		return this.labels[key];
	}
};
