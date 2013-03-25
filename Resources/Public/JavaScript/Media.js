"use strict";

/** @namespace Media */

$(document).ready(function () {

	// Enable the hide / show column
	$('.check-visible-toggle').click(function () {
		var iCol = $(this).val();

		/* Get the DataTables object again - this is not a recreation, just a get of the object */
		var oTable = $('#media-list').dataTable();

		var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
		oTable.fnSetColumnVis(iCol, bVis ? false : true);
	});

	$(document).keyup(function (e) {
		// escape
		var ESCAPE_KEY = 27
		if (e.keyCode == ESCAPE_KEY) {

			// True means the main panel is not currently displayed.
			if ($('#navbar-sub > *').length > 0) {
				var noRedraw = false;
				Media.Panel.showList(noRedraw);
			}
		}
	});

	Media.table = $('#media-list').dataTable(Media.Table.getOptions());
	Media.Session.initialize();
});

/**
 * Format a string give a place holder. Acts as the "sprintf" function in PHP
 *
 * Example:
 *
 * "Foo {0}".format('Bar') will return "Foo Bar"
 *
 * @param {string} key
 */
Media.format = function (key) {
	var s = Media.label(key),
		i = arguments.length + 1;

	while (i--) {
		s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i + 1]);
	}
	return s;
};

/**
 * Shorthand method for getting a label.
 *
 * @param {string} key
 */
Media.label = function (key) {
	return Media.Label.get(key);
};


/**
 * Merge second object into first
 *
 * @param {object} set1
 * @param {object} set2
 * @return {object}
 */
Media.merge = function (set1, set2) {
	for (var key in set2) {
		if (set2.hasOwnProperty(key))
			set1[key] = set2[key]
	}
	return set1
}
