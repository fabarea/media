<?php
namespace TYPO3\CMS\Media\Override\Backend\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2013 Kasper Skårhøj (kasperYYYY@typo3.com)
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
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * 'TCEforms' - Class for creating the backend editing forms.
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 * @coauthor René Fritz <r.fritz@colorcube.de>
 */
class FormEngine extends \TYPO3\CMS\Backend\Form\FormEngine {

	/**
	 * Used to connect the db/file browser with this document and the formfields on it!
	 *
	 * @param string $formObj Form object reference (including "document.")
	 * @return string JavaScript functions/code (NOT contained in a <script>-element)
	 */
	public function dbFileCon($formObj = 'document.forms[0]') {
		$enableMediaFilePicker = (bool) $GLOBALS['BE_USER']->getTSConfigVal('options.vidi.enableMediaFilePicker');
		if (! $enableMediaFilePicker) {
			return parent::dbFileCon($formObj);
		}

		$str = '
			// ***************
			// Used to connect the db/file browser with this document and the formfields on it!
			// ***************

			var browserWin="";

			function setFormValueOpenBrowser(mode,params) {

				// Changed here from original implementation - Fabien - 11.11.2013
				// Check if filter against file is required "&tx_vidi_user_vidisysfilem1[matches][type]=2"
				var url = "mod.php?M=user_VidiSysFileM1&tx_vidi_user_vidisysfilem1[plugins][]=filePicker&params=" + params;

				var name = "File Picker";
				var dimensions = {
					top: 0,
					left: 0,
					width: 1280,
					height: 800
				};
				browserWin = window.open(url, name, "toolbar=no,location=no,directories=no,menubar=no,resizable=yes,top=" + dimensions.top + ",left=" + dimensions.left + ",dependent=yes,dialog=yes,chrome=no,width=" + dimensions.width + ",height=" + dimensions.height + ",scrollbars=yes");
				browserWin.focus();
			}
			function setFormValueFromBrowseWin(fName,value,label,title,exclusiveValues) {
				var formObj = setFormValue_getFObj(fName), fObj, isMultiple = false, isList = false, len;
				if (formObj && value !== "--div--") {
						// Check if the form object has a "_list" element or not
						// The "_list" element exists for multiple selection select types
					if (formObj[fName + "_list"]) {
						fObj = formObj[fName + "_list"];
						isMultiple =  fObj.multiple && fObj.getAttribute("size") != "1";
						isList = true;
					} else {
						fObj = formObj[fName];
					}

						// clear field before adding value, if configured so (maxitems==1)
					if (typeof TBE_EDITOR.clearBeforeSettingFormValueFromBrowseWin[fName] != "undefined") {
						clearSettings = TBE_EDITOR.clearBeforeSettingFormValueFromBrowseWin[fName];
						setFormValueManipulate(fName, "Remove");

							// Clear the upload field
						var filesContainer = document.getElementById(clearSettings.itemFormElID_file);
						if(filesContainer) {
							filesContainer.innerHTML = filesContainer.innerHTML;
						}

							// update len after removing value
						len = fObj.length;
					}

					if (isMultiple || isList) {
						if (!isMultiple) {
								// If multiple values are not allowed, clear anything that is in the control already
							fObj.options.length = 0;
							fObj.length = 0; // Note: this is dangerous! "length" on the object is a reserved JS attribute!
						}
						len = fObj.length;

							// Clear elements if exclusive values are found
						if (exclusiveValues) {
							var m = new RegExp("(^|,)" + value + "($|,)");
							if (exclusiveValues.match(m)) {
									// the new value is exclusive
								for (a = len - 1; a >= 0; a--) {
									fObj[a] = null; // This is dangerous because it works on the object\'s numeric properties directly instead of using a custom attribute!
								}
								len = 0;
							} else if (len == 1) {
								m = new RegExp("(^|,)" + fObj.options[0].value + "($|,)");
								if (exclusiveValues.match(m)) {
										// the old value is exclusive
									fObj[0] = null;
									len = 0;
								}
							}
						}
							// Inserting element
						var setOK = true;
						if (!formObj[fName + "_mul"] || formObj[fName + "_mul"].value == 0) {
							for (a = 0; a < len; a++) {
								if (fObj.options[a].value == value) {
									setOK = false;
								}
							}
						}
						if (setOK) {
							fObj.length++;
							fObj.options[len].value = value;
							fObj.options[len].text = unescape(label);
							fObj.options[len].title = title;

								// Traversing list and set the hidden-field
							setHiddenFromList(fObj,formObj[fName]);
							' . $this->TBE_EDITOR_fieldChanged_func . '
						}
					} else {
							// The incoming value consists of the table name, an underscore and the uid
							// For a single selection field we need only the uid, so we extract it
						var uidValue = value;
						var pattern = /_(\\d+)$/;
						var result = value.match(pattern);
						if (result != null) {
							uidValue = result[1];
						}
							// Change the selected value
						fObj.value = uidValue;
					}
				}
			}
			function setHiddenFromList(fObjSel,fObjHid) {	//
				l=fObjSel.length;
				fObjHid.value="";
				for (a=0;a<l;a++) {
					fObjHid.value+=fObjSel.options[a].value+",";
				}
			}
			function setFormValueManipulate(fName, type, maxLength) {
				var formObj = setFormValue_getFObj(fName);
				if (formObj) {
					var localArray_V = new Array();
					var localArray_L = new Array();
					var localArray_S = new Array();
					var localArray_T = new Array();
					var fObjSel = formObj[fName+"_list"];
					var l=fObjSel.length;
					var c=0;

					if (type == "RemoveFirstIfFull") {
						if (maxLength == 1) {
							for (a = 1; a < l; a++) {
								if (fObjSel.options[a].selected != 1) {
									localArray_V[c] = fObjSel.options[a].value;
									localArray_L[c] = fObjSel.options[a].text;
									localArray_S[c] = 0;
									localArray_T[c] = fObjSel.options[a].title;
									c++;
								}
							}
						} else {
							return;
						}
					}

					if ((type=="Remove" && fObjSel.size > 1) || type=="Top" || type=="Bottom") {
						if (type=="Top") {
							for (a=0;a<l;a++) {
								if (fObjSel.options[a].selected==1) {
									localArray_V[c]=fObjSel.options[a].value;
									localArray_L[c]=fObjSel.options[a].text;
									localArray_S[c]=1;
									localArray_T[c] = fObjSel.options[a].title;
									c++;
								}
							}
						}
						for (a=0;a<l;a++) {
							if (fObjSel.options[a].selected!=1) {
								localArray_V[c]=fObjSel.options[a].value;
								localArray_L[c]=fObjSel.options[a].text;
								localArray_S[c]=0;
								localArray_T[c] = fObjSel.options[a].title;
								c++;
							}
						}
						if (type=="Bottom") {
							for (a=0;a<l;a++) {
								if (fObjSel.options[a].selected==1) {
									localArray_V[c]=fObjSel.options[a].value;
									localArray_L[c]=fObjSel.options[a].text;
									localArray_S[c]=1;
									localArray_T[c] = fObjSel.options[a].title;
									c++;
								}
							}
						}
					}
					if (type=="Down") {
						var tC = 0;
						var tA = new Array();

						for (a=0;a<l;a++) {
							if (fObjSel.options[a].selected!=1) {
									// Add non-selected element:
								localArray_V[c]=fObjSel.options[a].value;
								localArray_L[c]=fObjSel.options[a].text;
								localArray_S[c]=0;
								localArray_T[c] = fObjSel.options[a].title;
								c++;

									// Transfer any accumulated and reset:
								if (tA.length > 0) {
									for (aa=0;aa<tA.length;aa++) {
										localArray_V[c]=fObjSel.options[tA[aa]].value;
										localArray_L[c]=fObjSel.options[tA[aa]].text;
										localArray_S[c]=1;
										localArray_T[c] = fObjSel.options[tA[aa]].title;
										c++;
									}

									var tC = 0;
									var tA = new Array();
								}
							} else {
								tA[tC] = a;
								tC++;
							}
						}
							// Transfer any remaining:
						if (tA.length > 0) {
							for (aa=0;aa<tA.length;aa++) {
								localArray_V[c]=fObjSel.options[tA[aa]].value;
								localArray_L[c]=fObjSel.options[tA[aa]].text;
								localArray_S[c]=1;
								localArray_T[c] = fObjSel.options[tA[aa]].title;
								c++;
							}
						}
					}
					if (type=="Up") {
						var tC = 0;
						var tA = new Array();
						var c = l-1;

						for (a=l-1;a>=0;a--) {
							if (fObjSel.options[a].selected!=1) {

									// Add non-selected element:
								localArray_V[c]=fObjSel.options[a].value;
								localArray_L[c]=fObjSel.options[a].text;
								localArray_S[c]=0;
								localArray_T[c] = fObjSel.options[a].title;
								c--;

									// Transfer any accumulated and reset:
								if (tA.length > 0) {
									for (aa=0;aa<tA.length;aa++) {
										localArray_V[c]=fObjSel.options[tA[aa]].value;
										localArray_L[c]=fObjSel.options[tA[aa]].text;
										localArray_S[c]=1;
										localArray_T[c] = fObjSel.options[tA[aa]].title;
										c--;
									}

									var tC = 0;
									var tA = new Array();
								}
							} else {
								tA[tC] = a;
								tC++;
							}
						}
							// Transfer any remaining:
						if (tA.length > 0) {
							for (aa=0;aa<tA.length;aa++) {
								localArray_V[c]=fObjSel.options[tA[aa]].value;
								localArray_L[c]=fObjSel.options[tA[aa]].text;
								localArray_S[c]=1;
								localArray_T[c] = fObjSel.options[tA[aa]].title;
								c--;
							}
						}
						c=l;	// Restore length value in "c"
					}

						// Transfer items in temporary storage to list object:
					fObjSel.length = c;
					for (a=0;a<c;a++) {
						fObjSel.options[a].value = localArray_V[a];
						fObjSel.options[a].text = localArray_L[a];
						fObjSel.options[a].selected = localArray_S[a];
						fObjSel.options[a].title = localArray_T[a];
					}
					setHiddenFromList(fObjSel,formObj[fName]);

					' . $this->TBE_EDITOR_fieldChanged_func . '
				}
			}
			function setFormValue_getFObj(fName) {	//
				var formObj = ' . $formObj . ';
				if (formObj) {
						// Take the form object if it is either of type select-one or of type-multiple and it has a "_list" element
					if (formObj[fName] &&
						(
							(formObj[fName].type == "select-one") ||
							(formObj[fName + "_list"] && formObj[fName + "_list"].type.match(/select-(one|multiple)/))
						)
					) {
						return formObj;
					} else {
						alert("Formfields missing:\\n fName: " + formObj[fName] + "\\n fName_list:" + formObj[fName + "_list"] + "\\n type:" + formObj[fName + "_list"].type + "\\n fName:" + fName);
					}
				}
				return "";
			}

			// END: dbFileCon parts.
		';
		return $str;
	}
}

?>
