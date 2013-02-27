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
				Media.Panel.showList();
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


/**
 * Parse the input and return the content within the body tag.
 *
 * @param {string} data
 * @return string
 */
Media.getBodyContent = function(data) {
	var result, pattern, parts;
	pattern = /<body[^>]*>((.|[\n\r])*)<\/body>/im;
	parts = pattern.exec(data);

	// parts[0] corresponds to the body
	if (parts[0] != 'undefined') {
		result = parts[0];
	}
	return result;
}

/**
 * Return a cleaned source and evaluate JavaScript if found among "data".
 *
 * @see http://stackoverflow.com/questions/10888326/executing-javascript-script-after-ajax-loaded-a-page-doesnt-work
 * @param {string} data
 * @return string
 */
Media.parseScript = function(data) {
	var source = data;
	var scripts = new Array();

	// Strip out tags
	while (source.indexOf("<script") > -1 || source.indexOf("</script") > -1) {
		var s = source.indexOf("<script");
		var s_e = source.indexOf(">", s);
		var e = source.indexOf("</script", s);
		var e_e = source.indexOf(">", e);

		// Add to scripts array
		scripts.push(source.substring(s_e + 1, e));
		// Strip from source
		source = source.substring(0, s) + source.substring(e_e + 1);
	}

	// Loop through every script collected and eval it
	for (var i = 0; i < scripts.length; i++) {
		try {
			$.globalEval(scripts[i]);
		}
		catch (ex) {
			// do what you want here when a script fails
		}
	}
	// Return the cleaned source
	return source;
}