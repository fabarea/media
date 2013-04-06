"use strict";

/** @namespace Media */

/**
 * Object for handling Data Table
 *
 * @type {Object}
 */
Media.Table = {

	/**
	 * @return object
	 */
	getOptions: function () {

		/**
		 * Table initial options.
		 *
		 * Internal note: properties of Datatables have prefix: m, b, s, i, o, a, fn etc...
		 * this corresponds to the variable type e.g. mixed, boolean, string, integer, object, array, function
		 */
		return {
			'bStateSave': true,
			'iCookieDuration': 43200, // 12 hours
			'bProcessing': true,
			'bServerSide': true,
			'sAjaxSource': "/typo3/mod.php",
			'oLanguage': {
				// remove some label
				"sSearch": '',
				"sLengthMenu": '_MENU_'
			},

			/**
			 * Add Ajax parameters from plug-ins
			 *
			 * @param {object} aoData dataTables settings object
			 * @return void
			 */
			"fnServerParams": function (aoData) {

				// Get the parameter related to filter from the URL and "re-inject" them into the Ajax request
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
				aoData.push({ "name": 'M', "value": 'user_MediaM1' });
			},
			'aoColumns': Media._columns,
			'aLengthMenu': [
				[10, 25, 50, 100, -1],
				[10, 25, 50, 100, "All"]
			],
			'fnInitComplete': function () {
				Media.Table.animateRow();
			},
			'fnDrawCallback': function () {

				// Attach event to DOM elements
				Media.Action.edit();
				Media.Action.linkMaker();
				Media.Action.imageMaker();
				Media.Action.delete();

				// Handle flash message
				Media.FlashMessage.showAll();
			}
		};
	},

	/**
	 * Apply effect telling the User a row was edited.
	 *
	 * @return void
	 * @private
	 */
	animateRow: function () {

		// Only if User has previously edited a record.
		if (Media.Session.has('media.lastEditedUid')) {
			var uid = Media.Session.get('media.lastEditedUid');

			// Wait a little bit before applying fade-int class. Look nicer.
			setTimeout(function () {
				$('#row-' + uid).addClass('fade-in');
			}, 100);
			setTimeout(function () {
				$('#row-' + uid).addClass('fade-out').removeClass('fade-in');

				// Reset last edited uid
				Media.Session.reset('media.lastEditedUid');
			}, 500);
		}
	}
};

