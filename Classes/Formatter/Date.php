<?php
namespace Fab\Media\Formatter;

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

/**
 * Format a date that will be displayed in the Grid
 */
class Date {

	/**
	 * Format a date
	 *
	 * @param string $value
	 * @return string
	 */
	public function format($value) {
		/** @var $viewHelper \TYPO3\CMS\Fluid\ViewHelpers\Format\DateViewHelper */
		$viewHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Fluid\ViewHelpers\Format\DateViewHelper');
		return $viewHelper->render('@' . $value, 'd.m.Y');
	}

}
