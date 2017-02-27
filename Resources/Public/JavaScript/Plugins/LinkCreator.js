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
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/UserAgent/UserAgent',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/DOM/DOM',
	'TYPO3/CMS/Rtehtmlarea/HTMLArea/Util/Util'
], function(Plugin, UserAgent, Dom, Util) {

	var LinkCreator = function(editor, pluginName) {
		this.constructor.super.call(this, editor, pluginName);
	};

	Util.inherit(LinkCreator, Plugin);
	Util.apply(LinkCreator.prototype, {

		/**
		 * This function gets called by the class constructor
		 */
		configurePlugin: function(editor) {

			this.pageTSConfiguration = this.editorConfiguration.buttons.linkcreator;
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
			var buttonId = "LinkCreator";
			var buttonConfiguration = {
				id: buttonId,
				tooltip: '',
				iconCls: 'htmlarea-link-creator',
				action: "onButtonPress",
				context: "a",
				selection: false,
				dialog: true
			};
			this.registerButton(buttonConfiguration);

			return true;
		},

		/**
		 * This function gets called when the button was pressed.
		 *
		 * @param {Object} editor the editor instance
		 * @param {string} id the button id or the key
		 * @return boolean    false if action is completed
		 */
		onButtonPress: function(editor, id) {
			var params = {};

			this.elementNode = this.editor.getSelection().getFirstAncestorOfType('a');
			// true means there is an existing link selected in the RTE
			if (this.elementNode != null && /^a$/i.test(this.elementNode.nodeName)) {
				params.selection = this.elementNode;
			}
			else {
				//params.selection = this.editor.getSelectedHTML();
				this.elementNode = '';
			}

			var name = 'Media LinkCreator';
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
			this.dialogWindow.opener.Media.LinkCreator = this;

			return false;
		},

		/**
		 * Call back method. Insert document link.
		 *
		 * @param {Object} params the selected image
		 * @return boolean
		 */
		createDocumentLink: function(params) {
			// true means the link already exists
			if (typeof(this.elementNode) == 'object') {
				this.elementNode.setAttribute('title', params.titleAttribute);
				this.elementNode.setAttribute('class', params.classAttribute);
				this.elementNode.setAttribute('target', params.targetAttribute);
				this.elementNode.setAttribute('href', params.hrefAttribute);
			}
			// this is a new link
			else if (params) {
				this.createLink(params.hrefAttribute, params.targetAttribute, params.classAttribute, params.titleAttribute, false);
			}
			return false;
		},


		/*
		 * This function gets called when the toolbar is updated
		 */
		onUpdateToolbar: function(button, mode, selectionEmpty, ancestors) {
			if (mode === 'wysiwyg' && this.editor.isEditable() && button.itemId === 'LinkCreator') {
				button.setDisabled(selectionEmpty && !button.isInContext(mode, selectionEmpty, ancestors));
				if (!button.disabled) {
					var node = this.editor.getSelection().getParentElement();
					var el = this.editor.getSelection().getFirstAncestorOfType('a');
					if (el != null) {
						node = el;
					}
					if (node != null && /^a$/i.test(node.nodeName)) {
						button.setTooltip(this.localize('Modify link'));
					} else {
						button.setTooltip(this.localize('Insert link'));
					}
				}
			}
		},

		/*******************************************/
		/* CODE TAKEN FROM typo3/sysext/rtehtmlarea/htmlarea/plugins/TYPO3Link/typo3link.js
		 /*******************************************/

		/*
		 * Add a link to the selection.
		 * This function is called from the TYPO3 link popup.
		 *
		 * @param	string	theLink: the href attribute of the link to be created
		 * @param	string	cur_target: value for the target attribute
		 * @param	string	cur_class: value for the class attribute
		 * @param	string	cur_title: value for the title attribute
		 * @param	object	additionalValues: values for additional attributes (may be used by extension)
		 *
		 * @return void
		 */
		createLink: function(theLink, cur_target, cur_class, cur_title, additionalValues) {


			var range, anchorClass, imageNode = null, addIconAfterLink;
			this.restoreSelection();
			var node = this.editor.getSelection().getFirstAncestorOfType('a');
			if (!node) {
				node = this.editor.getSelection().getParentElement();
			}
			if (HTMLArea.classesAnchorSetup && cur_class) {
				for (var i = HTMLArea.classesAnchorSetup.length; --i >= 0;) {
					anchorClass = HTMLArea.classesAnchorSetup[i];
					if (anchorClass.name == cur_class && anchorClass.image) {
						imageNode = this.editor.document.createElement('img');
						imageNode.src = anchorClass.image;
						imageNode.alt = anchorClass.altText;
						addIconAfterLink = anchorClass.addIconAfterLink;
						break;
					}
				}
			}
			if (node != null && /^a$/i.test(node.nodeName)) {
				// Update existing link
				this.editor.getSelection().selectNode(node);
				range = this.editor.getSelection().createRange();
				// Clean images, keep links
				if (HTMLArea.classesAnchorSetup) {
					this.cleanAllLinks(node, range, true);
				}
				// Update link href
				// In IE, setting href may update the content of the element. We don't want this feature.
				if (UserAgent.isIE) {
					var content = node.innerHTML;
				}
				node.href = UserAgent.isGecko ? encodeURI(theLink) : theLink;
				if (UserAgent.isIE) {
					node.innerHTML = content;
				}
				// Update link attributes
				this.setLinkAttributes(node, range, cur_target, cur_class, cur_title, imageNode, addIconAfterLink, additionalValues);
			} else {
				// Create new link
				// Cleanup selected range
				range = this.editor.getSelection().createRange();
				// Clean existing anchors otherwise Mozilla may create nested anchors while IE may update existing link
				if (HTMLArea.isIEBeforeIE9) {
					this.cleanAllLinks(node, range, true);
					this.editor.getSelection().execCommand('UnLink', false, null);
				} else {
					// Selection may be lost when cleaning links
					// Note: In IE6-8, the following procedure breaks the selection used by the execCommand
					var bookMark = this.editor.getBookMark().get(range);
					this.cleanAllLinks(node, range);
					range = this.editor.getBookMark().moveTo(bookMark);
					this.editor.getSelection().selectRange(range);
				}
				if (UserAgent.isGecko) {
					this.editor.getSelection().execCommand('CreateLink', false, encodeURI(theLink));
				} else {
					this.editor.getSelection().execCommand('CreateLink', false, theLink);
				}
				// Get the created link or parent
				node = this.editor.getSelection().getParentElement();
				// Re-establish the range of the selection
				range = this.editor.getSelection().createRange();
				if (node) {
					// Export trailing br that IE may include in the link
					if (UserAgent.isIE) {
						if (node.lastChild && /^br$/i.test(node.lastChild.nodeName)) {
							Dom.removeFromParent(node.lastChild);
							node.parentNode.insertBefore(this.editor.document.createElement('br'), node.nextSibling);
						}
					}
					// We may have created multiple links in as many blocks
					this.setLinkAttributes(node, range, cur_target, cur_class, cur_title, imageNode, addIconAfterLink, additionalValues);
				}
			}
		},

		/*
		 * Unlink the selection.
		 * This function is called from the TYPO3 link popup and from unlink button pressed in toolbar or context menu.
		 *
		 * @param	string	buttonPressd: true if the unlink button was pressed
		 *
		 * @return void
		 */
		unLink: function(buttonPressed) {

			// If no dialogue window was opened, the selection should not be restored
			if (!buttonPressed) {
				this.restoreSelection();
			}
			var node = this.editor.getSelection().getParentElement();
			var el = this.editor.getSelection().getFirstAncestorOfType('a');
			if (el != null) {
				node = el;
			}
			if (node != null && /^a$/i.test(node.nodeName)) {
				this.editor.getSelection().selectNode(node);
			}
			if (HTMLArea.classesAnchorSetup) {
				var range = this.editor.getSelection().createRange();
				if (!HTMLArea.isIEBeforeIE9) {
					this.cleanAllLinks(node, range, false);
				} else {
					this.cleanAllLinks(node, range, true);
					this.editor.getSelection().execCommand('Unlink', false, '');
				}
			} else {
				this.editor.getSelection().execCommand('Unlink', false, '');
			}
		},

		/*
		 * Set attributes of anchors intersecting a range in the given node
		 *
		 * @param	object	node: a node that may interesect the range
		 * @param	object	range: set attributes on all nodes intersecting this range
		 * @param	string	cur_target: value for the target attribute
		 * @param	string	cur_class: value for the class attribute
		 * @param	string	cur_title: value for the title attribute
		 * @param	object	imageNode: image to clone and append to the anchor
		 * @param	boolean	addIconAfterLink: add icon after rather than before the link
		 * @param	object	additionalValues: values for additional attributes (may be used by extension)
		 *
		 * @return	void
		 */
		setLinkAttributes: function(node, range, cur_target, cur_class, cur_title, imageNode, addIconAfterLink, additionalValues) {

			if (/^a$/i.test(node.nodeName)) {
				var nodeInRange = false;
				if (!HTMLArea.isIEBeforeIE9) {
					this.editor.focus();
					nodeInRange = Dom.rangeIntersectsNode(range, node);
				} else {
					if (this.editor.getSelection().getType() === 'Control') {
						// we assume an image is selected
						nodeInRange = true;
					} else {
						var nodeRange = this.editor.document.body.createTextRange();
						nodeRange.moveToElementText(node);
						nodeInRange = nodeRange.inRange(range) || range.inRange(nodeRange) || (range.compareEndPoints('StartToStart', nodeRange) == 0) || (range.compareEndPoints('EndToEnd', nodeRange) == 0);
					}
				}
				if (nodeInRange) {
					if (imageNode != null) {
						if (addIconAfterLink) {
							node.appendChild(imageNode.cloneNode(false));
						} else {
							node.insertBefore(imageNode.cloneNode(false), node.firstChild);
						}
					}
					if (UserAgent.isGecko) {
						node.href = decodeURI(node.href);
					}
					if (cur_target.trim()) node.target = cur_target.trim();
					else node.removeAttribute('target');
					if (cur_class.trim()) {
						node.className = cur_class.trim();
					} else {
						if (!UserAgent.isOpera) {
							node.removeAttribute('class');
							if (HTMLArea.isIEBeforeIE9) {
								node.removeAttribute('className');
							}
						} else {
							node.className = '';
						}
					}
					if (cur_title.trim()) {
						node.title = cur_title.trim();
					} else {
						node.removeAttribute('title');
						node.removeAttribute('rtekeep');
					}
					if (this.pageTSConfiguration && this.pageTSConfiguration.additionalAttributes && typeof(additionalValues) == 'object') {
						for (additionalAttribute in additionalValues) {
							if (additionalValues.hasOwnProperty(additionalAttribute)) {
								if (additionalValues[additionalAttribute].toString().trim()) {
									node.setAttribute(additionalAttribute, additionalValues[additionalAttribute]);
								} else {
									node.removeAttribute(additionalAttribute);
								}
							}
						}
					}
				}
			} else {
				for (var i = node.firstChild; i; i = i.nextSibling) {
					if (i.nodeType === Dom.ELEMENT_NODE || i.nodeType === Dom.DOCUMENT_FRAGMENT_NODE) {
						this.setLinkAttributes(i, range, cur_target, cur_class, cur_title, imageNode, addIconAfterLink, additionalValues);
					}
				}
			}
		},


		/*
		 * Clean up images in special anchor classes
		 */
		cleanClassesAnchorImages: function(node) {
			var nodeArray = [], splitArray1 = [], splitArray2 = [];
			for (var childNode = node.firstChild; childNode; childNode = childNode.nextSibling) {
				if (/^img$/i.test(childNode.nodeName)) {
					splitArray1 = childNode.src.split('/');
					for (var i = HTMLArea.classesAnchorSetup.length; --i >= 0;) {
						if (HTMLArea.classesAnchorSetup[i]['image']) {
							splitArray2 = HTMLArea.classesAnchorSetup[i]['image'].split('/');
							if (splitArray1[splitArray1.length - 1] == splitArray2[splitArray2.length - 1]) {
								nodeArray.push(childNode);
								break;
							}
						}
					}
				}
			}
			for (i = nodeArray.length; --i >= 0;) {
				node.removeChild(nodeArray[i]);
			}
		},

		/*
		 * Clean up all anchors intesecting with the range in the given node
		 */
		cleanAllLinks: function(node, range, keepLinks) {
			if (/^a$/i.test(node.nodeName)) {
				var intersection = false;
				if (!HTMLArea.isIEBeforeIE9) {
					this.editor.focus();
					intersection = Dom.rangeIntersectsNode(range, node);
				} else {
					if (this.editor.getSelection().getType() === 'Control') {
						// we assume an image is selected
						intersection = true;
					} else {
						var nodeRange = this.editor.document.body.createTextRange();
						nodeRange.moveToElementText(node);
						intersection = range.inRange(nodeRange) || ((range.compareEndPoints('StartToStart', nodeRange) > 0) && (range.compareEndPoints('StartToEnd', nodeRange) < 0)) || ((range.compareEndPoints('EndToStart', nodeRange) > 0) && (range.compareEndPoints('EndToEnd', nodeRange) < 0));
					}
				}
				if (intersection) {
					this.cleanClassesAnchorImages(node);
					if (!keepLinks) {
						while (node.firstChild) {
							node.parentNode.insertBefore(node.firstChild, node);
						}
						node.parentNode.removeChild(node);
					}
				}
			} else {
				var child = node.firstChild,
					nextSibling;
				while (child) {
					// Save next sibling as child may be removed
					nextSibling = child.nextSibling;
					if (child.nodeType === Dom.ELEMENT_NODE || child.nodeType === Dom.DOCUMENT_FRAGMENT_NODE) {
						this.cleanAllLinks(child, range, keepLinks);
					}
					child = nextSibling;
				}
			}
		}

	});

	return LinkCreator;

});
