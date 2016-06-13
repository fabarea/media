/*jshint -W030 */
/**
 * Released under the GPL v2+, part of TYPO3
 *
 * Override default JavaScript file located at typo3/sysext/backend/Resources/Public/JavaScript/FormEngine.js
 */

// add legacy functions to be accessible in the global scope
var setFormValueOpenBrowser;

define('TYPO3/CMS/Media/MediaFormEngine', ['jquery', 'TYPO3/CMS/Backend/FormEngine'], function($) {

	var MediaFormEngine = {};

	// main options
	var FormEngine = {
		formName: TYPO3.settings.FormEngine.formName
		,backPath: TYPO3.settings.FormEngine.backPath
		,openedPopupWindow: null
		,legacyFieldChangedCb: function() { !$.isFunction(TYPO3.settings.FormEngine.legacyFieldChangedCb) || TYPO3.settings.FormEngine.legacyFieldChangedCb(); }
	};

	/**
	 * opens a popup window with the element browser (browser.php)
	 *
	 * @param mode can be "db" or "file"
	 * @param params additional params for the browser window
	 */
	FormEngine.openPopupWindow = setFormValueOpenBrowser = function(mode, params) {
		var url;

		// Open the Media Picker if we encounter an inline segment in the params -> file to be selected.
		if (params.search(/inline\./) > -1) {

			// An additional filter could be applied if required "&prefix[matches][type]=2"
			url = FormEngine.backPath + MediaFormEngine.vidiModuleUrl + '&' + MediaFormEngine.vidiModulePrefix + '[plugins][]=filePicker&params=' + params;

			//var name = "File Picker"; // Commented since IE compatibility issue. Weird!
			var dimensions = {
				top: 0,
				left: 0,
				width: 1280,
				height: 800
			};

			FormEngine.openedPopupWindow = window.open(url, "Typo3WinBrowser", "toolbar=no,location=no,directories=no,menubar=no,resizable=yes,top=" + dimensions.top + ",left=" + dimensions.left + ",dependent=yes,dialog=yes,chrome=no,width=" + dimensions.width + ",height=" + dimensions.height + ",scrollbars=yes");
			FormEngine.openedPopupWindow.focus();

		} else {
			// Default record picker as we know it...
			url = FormEngine.backPath + MediaFormEngine.browserUrl + '&mode=' + mode + '&bparams=' + params;
			FormEngine.openedPopupWindow = window.open(url, 'Typo3WinBrowser', 'height=650,width=' + (mode === 'db' ? 650 : 600) + ',status=0,menubar=0,resizable=1,scrollbars=1');
			FormEngine.openedPopupWindow.focus();
		}
	};


	return MediaFormEngine;

});
