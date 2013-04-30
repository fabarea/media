"use strict";

/** @namespace Media */
/**
 * Object for handling event and their actions
 *
 * @type {Object} Event
 */
Media.Rte = {
	Popup: {

		/**
		 * Create image link.
		 *
		 * @private
		 * @param {object} element
		 * @return void
		 */
		createImage: function (data) {

			var params = {};
			var hostAndProtocol = location.protocol + '//' + location.host + '/';
			params.tag = '<img src="' + hostAndProtocol + data.publicUrl + '?' + data.timeStamp +
				'" data-htmlarea-original-uid="' + data.original +
				'" data-htmlarea-file-uid="' + data.uid +
				'" data-htmlarea-file-table="sys_file" ';

			if (window.opener) {
				// apply previous classes
				if (window.opener.Media.ImageMaker.elementNode.className != null) {
					params.tag += 'class="' + window.opener.Media.ImageMaker.elementNode.className + '" '
				}

				// apply previous styles
				if (window.opener.Media.ImageMaker.elementNode.style != null &&
					window.opener.Media.ImageMaker.elementNode.style.cssText != null) {
					params.tag += 'style="' + window.opener.Media.ImageMaker.elementNode.style.cssText + '" '
				}
				params.tag += '/>';

				// write the "img" tag in the RTE
				window.opener.Media.ImageMaker.insertImage(params);
				window.close();
			}

		}
	}
};

