<?php
namespace Fab\Media\TypeConverter;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * Convert a Content Object to File
 */
class ContentToFileConverter implements SingletonInterface {

	/**
	 * Convert a file representation to File Resource.
	 *
	 * @param Content|int $fileRepresentation
	 * @throws \RuntimeException
	 * @return File
	 */
	public function convert($fileRepresentation) {

		if ($fileRepresentation instanceof Content) {

			$fileData = $fileRepresentation->toArray();

			if (!isset($fileData['storage']) && $fileData['storage'] === NULL) {
				throw new \RuntimeException('Storage identifier can not be null.', 1379946981);
			}

			$fileUid = $fileData['uid'];
		} else {
			$fileData = array();
			$fileUid = (int)$fileRepresentation;
		}
		return ResourceFactory::getInstance()->getFileObject($fileUid, $fileData);
	}
}