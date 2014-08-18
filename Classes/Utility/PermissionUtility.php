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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * A class for handling permission
 */
class PermissionUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\PermissionUtility
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\PermissionUtility');
	}

	/**
	 * Returns allowed extensions given a possible storage.
	 *
	 * @param int $storageIdentifier
	 * @return array
	 */
	public function getAllowedExtensions($storageIdentifier = 0) {

		$fieldNames = array(
			'extension_allowed_file_type_1',
			'extension_allowed_file_type_2',
			'extension_allowed_file_type_3',
			'extension_allowed_file_type_4',
			'extension_allowed_file_type_5',
		);

		if ($storageIdentifier > 0) {
			$storage = ObjectFactory::getInstance()->getStorage($storageIdentifier);
		} else {
			$storage = StorageUtility::getInstance()->getCurrentStorage();
		}

		$storageRecord = $storage->getStorageRecord();
		$allowedExtensions = array();
		foreach ($fieldNames as $fieldName) {
			$_allowedExtensions = GeneralUtility::trimExplode(',', $storageRecord[$fieldName], TRUE);
			$allowedExtensions = array_merge($allowedExtensions, $_allowedExtensions);
		}

		$uniqueAllowedExtensions = array_unique($allowedExtensions);
		return array_filter($uniqueAllowedExtensions, 'strlen');
	}

	/**
	 * Returns allowed extensions list.
	 *
	 * @return string
	 */
	public function getAllowedExtensionList() {
		return implode(',', $this->getAllowedExtensions());
	}

}
