"use strict";

/** @namespace Media */

$(document).ready(function () {

	// Initialize Session
	Media.Session.initialize();

	/**
	 * Enable the hide / show column
	 */
	$('.check-visible-toggle').click(function () {
		var iCol = $(this).val();

		/* Get the DataTables object again - this is not a recreation, just a get of the object */
		var oTable = $('#media-list').dataTable();

		var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
		oTable.fnSetColumnVis(iCol, bVis ? false : true);
	});

	/**
	 * Select or deselect all rows at once.
	 */
	$('.checkbox-row-top').click(function () {
		var checkboxes;
		checkboxes = $('#media-list').find('.checkbox-row');
		if ($(this).is(':checked')) {
			checkboxes.filter(':not(:checked)').click();
			$('.mass-action').removeClass('disabled');
		} else {
			checkboxes.filter(':checked').click();
			$('.mass-action').addClass('disabled');
		}
	});

	/**
	 * Mass delete action
	 */
	$('.mass-delete').click(function (e) {
		var checkboxes = [];
		$('#media-list')
			.find('.checkbox-row')
			.filter(':checked')
			.each(function() {
				checkboxes.push($(this).data('uid'));
			});

		alert('selected uid(s): ' + checkboxes.join(','))
		e.preventDefault();
	});

	/**
	 * Add Access Key for switching back to the Grid with key escape
 	 */
	$(document).keyup(function (e) {
		// escape
		var ESCAPE_KEY = 27;
		if (e.keyCode == ESCAPE_KEY) {

			// True means the main panel is not currently displayed.
			if ($('#navbar-sub > *').length > 0) {
				var noRedraw = false;
				Media.Panel.showList(noRedraw);
			}
		}
	});

	/**
	 * Initialize Grid
	 */
	Media.table = $('#media-list').dataTable(Media.Table.getOptions());

	// Add place holder for the search
	$('.dataTables_filter input').attr('placeholder', Media.translate('search'));
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
	var s = Media.translate(key),
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
Media.translate = function (key) {
	return Media.Label.get(key);
};

/**
 * Merge second object into first one
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
};