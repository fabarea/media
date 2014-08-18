<?php
namespace TYPO3\CMS\Media\Domain\Validator;

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

/**
 * Validate whether "storageIdentifier" is allowed.
 */
class StorageValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * Check if $storageIdentifier is allowed. If it is not valid, throw an exception.
	 *
	 * @param int $storageIdentifier
	 * @return void
	 */
	public function isValid($storageIdentifier) {

		if ((int) $storageIdentifier > 0) {
			$storageIdentifiers = array();
			foreach ($this->getStorageService()->findByBackendUser() as $storage) {
				$storageIdentifiers[] = $storage->getUid();
			}

			if (!in_array($storageIdentifier, $storageIdentifiers)) {
				$message = sprintf('Storage identifier "%s" is not allowed or is currently off-line.', $storageIdentifier);
				$this->addError($message , 1380813503);
			}
		}
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\StorageService
	 */
	protected function getStorageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\StorageService');
	}
}
