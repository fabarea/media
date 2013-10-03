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
 * Validate whether "assetIdentifier" exists.
 */
class AssetValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * Check whether $assetIdentifier exists. If it is not valid, throw an exception.
	 *
	 * @param int $assetIdentifier
	 * @return void
	 */
	public function isValid($assetIdentifier = NULL) {

		if ((int) $assetIdentifier > 0) {

			/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

			/** @var $assetRepository \TYPO3\CMS\Media\Domain\Repository\AssetRepository */
			$assetRepository = $objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');
			$asset = $assetRepository->findByIdentifier($assetIdentifier);

			if (!$asset) {
				$message = sprintf('Asset with identifier "%s" could not be found.', $assetIdentifier);
				$this->addError($message , 1380813504);
			}

		}
	}
}

?>