<?php
namespace TYPO3\CMS\Media\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * View helper which allows you to include a CSS File.
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class CssViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Compute a CSS tag and render it
	 *
	 * @param string $name the file to include
	 * @param string $extKey the extension, where the file is located
	 * @param string $pathInsideExt the path to the file relative to the ext-folder
	 * @return string
	 */
	public function render($name = NULL, $extKey = NULL, $pathInsideExt = 'Resources/Public/StyleSheet/') {

		if ($extKey === NULL) {
			$extKey = $this->controllerContext->getRequest()->getControllerExtensionKey();
		}

		if (TYPO3_MODE === 'FE') {
			$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey);
			$extRelPath = substr($extPath, strlen(PATH_site));
		} else {
			$extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey);
		}

		return sprintf('<link href="%s%s%s" rel="stylesheet" />', $extRelPath, $pathInsideExt, $name);
	}

}

?>