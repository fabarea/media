<?php
namespace Fab\Media\Override\Backend\Form;

/**
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

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * 'TCEforms' - Class for creating the backend editing forms.
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 * @coauthor René Fritz <r.fritz@colorcube.de>
 */
class FormEngine extends \TYPO3\CMS\Backend\Form\FormEngine {

	public function JStop() {
		$result = parent::JStop();
		$result .= '
<script>
	var vidiModuleUrl = \'' . BackendUtility::getModuleUrl('user_VidiSysFileM1') . '\';
</script>
		';
		return $result;
	}

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

		$enableMediaFilePicker = (bool)$GLOBALS['BE_USER']->getTSConfigVal('options.vidi.enableMediaFilePicker');
		if (!$update && $enableMediaFilePicker) {
			/** @var $pageRenderer \TYPO3\CMS\Core\Page\PageRenderer */
			$pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();

			// Override JS.
			$pageRenderer->loadRequireJsModule('TYPO3/CMS/Media/FormEngine');
		}

		return $result;
	}
}
