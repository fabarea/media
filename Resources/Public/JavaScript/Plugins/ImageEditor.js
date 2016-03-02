/*
 * Document Map Plugin for TYPO3 htmlArea RTE
 */

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * About Plugin for TYPO3 htmlArea RTE
 */
define(['TYPO3/CMS/Rtehtmlarea/HTMLArea/Plugin/Plugin',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Util/Util'
], function(Plugin, Util) {

	var ImageEditor = function(editor, pluginName) {
		this.constructor.super.call(this, editor, pluginName);
	};

	Util.inherit(ImageEditor, Plugin);
	Util.apply(ImageEditor.prototype, {

		/**
		 * This function gets called by the class constructor
		 */
		configurePlugin: function(editor) {

			this.pageTSConfiguration = this.editorConfiguration.buttons.imageeditor;
			this.modulePath = this.pageTSConfiguration.pathLinkModule;
			/*
			 * Registering plugin "About" information
			 */
			var pluginInformation = {
				version: "1.0",
				developer: "Fabien Udriot",
				developerUrl: "https://ecodev.ch/",
				copyrightOwner: "Fabien Udriot",
				sponsor: "Ecodev Sarl",
				sponsorUrl: "https://ecodev.ch",
				license: "GPL"
			};
			this.registerPluginInformation(pluginInformation);

			/*
			 * Registering the button
			 */
			var buttonId = "ImageEditor";
			var buttonConfiguration = {
				id: buttonId,
				tooltip: '',
				iconCls: 'htmlarea-image-editor',
				action: "onButtonPress",
				textMode: true,
				dialog: true
			};
			this.registerButton(buttonConfiguration);

			return true;
		},

		/*
		 * This function gets called when the button was pressed.
		 *
		 * @param object editor: the editor instance
		 * @param string id: the button id or the key
		 *
		 * @return boolean false if action is completed
		 */
		onButtonPress: function (editor, id) {
			var params = {};

			this.elementNode = editor.getSelection().getFirstAncestorOfType('img');
			// true means there is an existing link selected in the RTE
			if (this.elementNode != null && /^img$/i.test(this.elementNode.nodeName)) {
				params.selection = this.elementNode;
			}
			else {
				//params.selection = editor.getSelectedHTML();
				this.elementNode = '';
			}

			var name = 'Media ImageEditor';
			var dimensions = {
				top: 0,
				left: 0,
				width: 1280,
				height: 800
			};

			this.dialogWindow = window.open(
				this.modulePath,
				name,
				"toolbar=no,location=no,directories=no,menubar=no,resizable=yes,top=" + dimensions.top + ",left=" + dimensions.left + ",dependent=yes,dialog=yes,chrome=no,width=" + dimensions.width + ",height=" + dimensions.height + ",scrollbars=yes"
			);

			// Transmit this to the parent window (AKA the popup)
			this.dialogWindow.opener.Media = {};
			this.dialogWindow.opener.Media.ImageEditor = this;

			return false;
		},

		/*
		 * Call back method. Insert an image link.
		 *
		 * @param object param: the selected image
		 *
		 * @return boolean false
		 */
		insertImage: function (params) {
			if (params && typeof(params.tag) != "undefined") {
				// true means this has been a "slidshow" link
				//if (this.elementNode) {
				//	this.elementNode.parentNode.removeChild(this.elementNode);
				//}
				this.editor.getSelection().execCommand('insertHTML', false, params.tag);
			}
			return false;
		}
	});

	return ImageEditor;

});
