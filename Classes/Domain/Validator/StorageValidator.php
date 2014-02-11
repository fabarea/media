<?php
namespace TYPO3\CMS\Media\Domain\Validator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
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
			foreach (\TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorages() as $storage) {
				$storageIdentifiers[] = $storage->getUid();
			}

			if (!in_array($storageIdentifier, $storageIdentifiers)) {
				$message = sprintf('Storage identifier "%s" is not allowed or is currently off-line.', $storageIdentifier);
				$this->addError($message , 1380813503);
			}

		}
	}
}
