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

Ecoimages = HTMLArea.Plugin.extend({

	constructor : function(editor, pluginName) {
		this.base(editor, pluginName);
	},

	/*
	 * This function gets called by the class constructor
	 */
	configurePlugin : function(editor) {

		/*
		 * Registering plugin "About" information
		 */
		var pluginInformation = {
			version		: "1.0",
			developer	: "Fabien Udriot",
			developerUrl	: "http://ecodev.ch/",
			copyrightOwner	: "Fabien Udriot",
			sponsor		: "Ecodev Sarl",
			sponsorUrl	: "http://ecodev.ch",
			license		: "GPL"
		};
		this.registerPluginInformation(pluginInformation);

		/*
		 * Registering the button
		 */
		var buttonId = "Ecoimages";
		var buttonConfiguration = {
			id		: buttonId,
			tooltip		: this.localize("ecoimages"),
			action		: "onButtonPress",
			textMode	: true,
			dialog		: true
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
	onButtonPress : function(editor, id) {
		var url = '/typo3conf/ext/media/mod1/tx_ecomedias/selectImage/';
		var params = new Object();

		var node = this.editor.getParentElement();
		this.elementNode = HTMLArea.getElementObject(node, "a");

		/*
		 * true when this is link <a> with rtekeep and rel tag
		 */
		if (this.elementNode != null
			&& /^a$/i.test(this.elementNode.nodeName)
			&& this.elementNode.hasAttribute('rel')
			&& this.elementNode.hasAttribute('rtekeep')){
				params.selection = this.elementNode;
		}
		else{
			params.selection = this.editor.getSelectedElement();
			this.elementNode = '';
		}

		var ecomediasUid = '';
		var image = HTMLArea.getElementObject(node, 'img');
		if (image && image.hasAttribute('data-ecomedias'))
			ecomediasUid = image.getAttribute('data-ecomedias');

		this.dialog = this.openDialog("Ecoimages", url + ecomediasUid, 'insertImage', params, {width:1100, height:700}, 'yes');
		return false;
	},

	/*
	 * Call back method. Insert an image link.
	 *
	 * @param object param: the selected image
	 *
	 * @return boolean false
	 */
	insertImage : function(params){
		if (params && typeof(params.file) != "undefined") {
			// true means this has been a "slidshow" link
			if (this.elementNode) {
				this.elementNode.parentNode.removeChild(this.elementNode);
			}
			this.editor.focusEditor();
			this.editor.insertHTML(params.file);
		}
		return false;
	}
});