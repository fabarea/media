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

use Fab\Media\Module\VidiModule;
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
	var vidiModuleUrl = \'' . BackendUtility::getModuleUrl(VidiModule::getSignature()) . '\';
	var vidiModulePrefix = \'' . VidiModule::getParameterPrefix() . '\';
</script>
		';
		return $result;
	}

	/**
	 * JavaScript code used for input-field evaluation.
	 *
	 * @param string $formname The identification of the form on the page.
	 * @param boolean $update Just extend/update existing settings, e.g. for AJAX call
	 * @return string A section with JavaScript - if $update is FALSE, embedded in <script></script>
	 */
	public function JSbottom($formname = 'forms[0]', $update = FALSE) {

		$result = parent::JSbottom($formname, $update);

		$enableMediaFilePicker = (bool)$this->getBackendUser()->getTSConfigVal('options.vidi.enableMediaFilePicker');
		if (!$update && $enableMediaFilePicker) {
			/** @var $pageRenderer \TYPO3\CMS\Core\Page\PageRenderer */
			$pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();

			// Override JS.
			$pageRenderer->loadRequireJsModule('TYPO3/CMS/Media/FormEngine');
		}

		return $result;
	}

	/**
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
	}
}
