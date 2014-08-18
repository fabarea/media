<?php
namespace TYPO3\CMS\Media\Utility;

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
 * A utility class for module
 */
class ModuleUtility {

	/**
	 * Returns the parameter prefix
	 *
	 * @return string
	 */
	static public function getParameterPrefix() {
		return 'tx_media_user_mediam1';
	}

	/**
	 * Returns the module signature
	 *
	 * @return string
	 */
	static public function getModuleSignature() {
		return 'user_MediaM1';
	}

	/**
	 * Returns the module signature
	 *
	 * @param string $action
	 * @param string $controller
	 * @return string
	 */
	static public function getUri($action, $controller) {
		return sprintf('%s&%s[action]=%s&%s[controller]=%s',
			BackendUtility::getModuleUrl(self::getModuleSignature()),
			self::getParameterPrefix(),
			$action,
			self::getParameterPrefix(),
			$controller
		);
	}
}
