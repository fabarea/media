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

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate whether "fileIdentifier" exists.
 */
class FileValidator extends AbstractValidator {

	/**
	 * Check whether $fileIdentifier exists. If not, trigger an error.
	 *
	 * @param int $fileIdentifier
	 * @return void
	 */
	public function isValid($fileIdentifier = NULL) {

		if ((int) $fileIdentifier > 0) {

			$file = ResourceFactory::getInstance()->getFileObject($fileIdentifier);

			if (!$file) {
				$message = sprintf('File with identifier "%s" could not be found.', $fileIdentifier);
				$this->addError($message , 1380813504);
			}

		}
	}
}
