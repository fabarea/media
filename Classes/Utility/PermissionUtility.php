<?php
namespace TYPO3\CMS\Media\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
?>
