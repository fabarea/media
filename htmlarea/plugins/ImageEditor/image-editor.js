/*
 * Image Plugin for TYPO3 htmlArea RTE
 */

HTMLArea.ImageEditor = Ext.extend(HTMLArea.Plugin, {

	/*
	 * This function gets called by the class constructor
	 */
	configurePlugin: function (editor) {
		this.pageTSConfiguration = this.editorConfiguration.buttons.imageeditor;
		this.modulePath = this.pageTSConfiguration.pathLinkModule;
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
		var buttonId = "ImageEditor";
		var buttonConfiguration = {
			id: buttonId,
			tooltip: this.localize("imageeditor"),
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

		this.elementNode = this.editor.getSelection().getFirstAncestorOfType('img');
		// true means there is an existing link selected in the RTE
		if (this.elementNode != null && /^img$/i.test(this.elementNode.nodeName)) {
			params.selection = this.elementNode;
		}
		else {
			params.selection = this.editor.getSelectedHTML();
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
			this.editor.insertHTML(params.tag);
		}
		return false;
	}
});