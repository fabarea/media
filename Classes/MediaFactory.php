<?php
namespace TYPO3\CMS\Media;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Factory class for Media objects.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage t3lib
 */
class MediaFactory implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @return \TYPO3\CMS\Media\MediaFactory
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Media\\MediaFactory');
	}

	/**
	 * Creates a media object from an array of file data. Requires a database
	 * row to be fetched.
	 *
	 * @param array $fileData
	 * @param string $objectType
	 * @return \TYPO3\CMS\Media\Domain\Model\Media
	 */
	public function createObject(array $fileData, $objectType = 'TYPO3\CMS\Media\Domain\Model\Media') {
		/** @var \TYPO3\CMS\Media\Domain\Model\Media $mediaObject */
		$mediaObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($objectType, $fileData);

		if (is_numeric($fileData['storage'])) {
			$resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
			$storageObject = $resourceFactory->getStorageObject($fileData['storage']);
			$mediaObject->setStorage($storageObject);
		}
		return $mediaObject;
	}

}


?>