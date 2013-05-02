/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This script is a modified version of a script published under the htmlArea License.
 *  A copy of the htmlArea License may be found in the textfile HTMLAREA_LICENSE.txt.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/*
 * Image Plugin for TYPO3 htmlArea RTE
 */

HTMLArea.ImageMaker = Ext.extend(HTMLArea.Plugin, {

	/*
	 * This function gets called by the class constructor
	 */
	configurePlugin: function (editor) {

		/*
		 * Registering plugin "About" information
		 */
		var pluginInformation = {
			version: "1.0",
			developer: "Fabien Udriot",
			developerUrl: "http://ecodev.ch/",
			copyrightOwner: "Fabien Udriot",
			sponsor: "Ecodev Sarl",
			sponsorUrl: "http://ecodev.ch",
			license: "GPL"
		};
		this.registerPluginInformation(pluginInformation);

		/*
		 * Registering the button
		 */
		var buttonId = "ImageMaker";
		var buttonConfiguration = {
			id: buttonId,
			tooltip: this.localize("imagemaker"),
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

		var url = 'mod.php?M=user_MediaM1&tx_media_user_mediam1[matches][type]=2&tx_media_user_mediam1[rtePlugin]=imageMaker';
		var params = new Object();

		this.elementNode = this.editor.getSelection().getFirstAncestorOfType('img');
		// true means there is an existing link selected in the RTE
		if (this.elementNode != null && /^img$/i.test(this.elementNode.nodeName)) {
			params.selection = this.elementNode;
		}
		else {
			params.selection = this.editor.getSelectedHTML();
			this.elementNode = '';
		}

		var name = 'Media ImageMaker';
		var dimensions = {
			top: 0,
			left: 0,
			width: 1280,
			height: 800
		};

		this.dialogWindow = window.open(url, name, "toolbar=no,location=no,directories=no,menubar=no,resizable=yes,top=" + dimensions.top + ",left=" + dimensions.left + ",dependent=yes,dialog=yes,chrome=no,width=" + dimensions.width + ",height=" + dimensions.height + ",scrollbars=yes");

		// Transmit this to the parent window (AKA the popup)
		this.dialogWindow.opener.Media = {};
		this.dialogWindow.opener.Media.ImageMaker = this;

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
			this.editor.insertHTML(params.tag);
		}
		return false;
	}
});