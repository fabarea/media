<?php
namespace TYPO3\CMS\Media\ViewHelpers;
/***************************************************************
*  Copyright notice
*
*  (c) 2012
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * View helper which returns the max upload file size.
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class MaxUploadSizeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Returns the max upload file size in Mo.
	 *
	 * @param string $label
	 * @param string $extensionName
	 * @return string
	 */
	public function render($label = NULL, $extensionName = 'media') {
		$result = \TYPO3\CMS\Core\Utility\GeneralUtility::getMaxUploadFileSize() / 1024;
		if ($label) {
			$label = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($label, $extensionName);
			$result = sprintf($label, $result);
		}
		return $result;
	}
}

?>