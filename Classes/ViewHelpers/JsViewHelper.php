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

/**
 * View helper which allows you to include a JS File.
 */
class JsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Compute a JS tag and render it
	 *
	 * @param string $name the file to include
	 * @param string $extensionName the extension, where the file is located
	 * @param string $pathInsideExt the path to the file relative to the ext-folder
	 * @return string the link
	 */
	public function render($name = NULL, $extensionName = NULL, $pathInsideExt = 'Resources/Public/JavaScript/') {

		if ($extensionName === NULL) {
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionKey();
		}

		if (TYPO3_MODE === 'FE') {
			$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionName);
			$extRelPath = substr($extPath, strlen(PATH_site));
		} else {
			$extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extensionName);
		}

		return sprintf('<script src="%s%s%s"></script>', $extRelPath, $pathInsideExt, $name);
	}

}
