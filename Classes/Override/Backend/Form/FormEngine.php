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
	 * JavaScript code used for input-field evaluation.
	 * Example use:
	 * $msg .= 'Distribution time (hh:mm dd-mm-yy):<br /><input type="text" name="send_mail_datetime_hr" onchange="typo3form.fieldGet(\'send_mail_datetime\', \'datetime\', \'\', 0,0);"' . $GLOBALS['TBE_TEMPLATE']->formWidth(20) . ' /><input type="hidden" value="' . $GLOBALS['EXEC_TIME'] . '" name="send_mail_datetime" /><br />';
	 * $this->extJSCODE.='typo3form.fieldSet("send_mail_datetime", "datetime", "", 0,0);';
	 * ... and then include the result of this function after the form
	 *
	 * @param string $formname The identification of the form on the page.
	 * @param boolean $update Just extend/update existing settings, e.g. for AJAX call
	 * @return string A section with JavaScript - if $update is FALSE, embedded in <script></script>
	 */
	public function JSbottom($formname = 'forms[0]', $update = FALSE) {

		$result = parent::JSbottom($formname, $update);

		$enableMediaFilePicker = (bool) $GLOBALS['BE_USER']->getTSConfigVal('options.vidi.enableMediaFilePicker');
		if (!$update && $enableMediaFilePicker) {

			/** @var $pageRenderer \TYPO3\CMS\Core\Page\PageRenderer */
			$pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();

			// Override JS.
			$pageRenderer->loadRequireJsModule('TYPO3/CMS/Media/FormEngine');
		}

		return $result;
	}
}
