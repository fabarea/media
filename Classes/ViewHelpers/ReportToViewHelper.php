<?php
namespace TYPO3\CMS\Media\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns the people who will receive a report.
 */
class ReportToViewHelper extends AbstractViewHelper {

	/**
	 * Returns the people who will receive a report.
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function render() {
		// @todo will be made more flexible at one point!
		return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
	}
}
