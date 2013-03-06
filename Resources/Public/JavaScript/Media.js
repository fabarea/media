"use strict";

/** @namespace Media */

$(document).ready(function () {

	// Binds form submission and fields to the validation engine
	$("#form-media").validationEngine();

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

	/**
	 * Table initialization
	 *
	 * Internal note: properties of Datatables have prefix: m, b, s, i, o, a, fn etc...
	 * this corresponds to the variable type e.g. mixed, boolean, string, integer, object, array, function
	 */
	Media.Table = $('#media-list').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/typo3/mod.php",
		"fnServerParams": function (aoData) {

			// Get the parameter from the main URL and re-inject them into the Ajax request
			var uri = new Uri(window.location.href);
			for (var index = 0; index < uri.queryPairs.length; index++) {
				var queryPair = uri.queryPairs[index];
				var parameterName = queryPair[0];
				var parameterValue = queryPair[1];
				var pattern = /tx_media_user_mediam1\[filter\]/g;
				if (pattern.test(parameterName)) {
					aoData.push({ "name": parameterName, "value": parameterValue });
				}
			}

			// Hand over the RTE plugin parameter
			var rtePluginParameter = 'tx_media_user_mediam1[rtePlugin]';
			if (uri.getQueryParamValue(rtePluginParameter)) {
				aoData.push({ "name": rtePluginParameter, "value": uri.getQueryParamValue(rtePluginParameter) });
			}

			aoData.push({ "name": 'M', "value": 'user_MediaM1' });
			aoData.push({ "name": 'tx_media_user_mediam1[action]', "value": 'listRow' });
			aoData.push({ "name": 'tx_media_user_mediam1[controller]', "value": 'Asset' });
			aoData.push({ "name": 'tx_media_user_mediam1[format]', "value": 'json' });
		},
		"aoColumns": Media._columns,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
		],
//		@todo can be removed if table tools utility get dropped - under investigation
//		"oTableTools": {
//			"sRowSelect": "multi",
//			"fnRowSelected": function (node) {
//				console.log(node.id);
//				console.log(node);
//			}
//		},
		"fnDrawCallback": function () {
			// Attach event to DOM elements
			Media.Action.edit();
			Media.Action.linkMaker();
			Media.Action.imageMaker();
			Media.Action.delete();

			// Handle flash message
			Media.FlashMessage.showAll();
		}
	});

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
