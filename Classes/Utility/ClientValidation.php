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

/**
 * A class to handle validation on the client side
 */
class ClientValidation implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Returns a class instance
	 *
	 * @return \TYPO3\CMS\Media\Utility\ClientValidation
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\ClientValidation');
	}

	/**
	 * Get the validation class name given a field.
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public function get($fieldName){
		$result = '';
		if (\TYPO3\CMS\Media\Utility\TcaField::getService()->isRequired($fieldName)) {
			$result = ' validate[required]';
		}
		return $result;
	}
}
