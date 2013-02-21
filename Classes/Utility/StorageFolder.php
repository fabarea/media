<?php
namespace TYPO3\CMS\Media\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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
 * A class to handle the default folder of the storage
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class StorageFolder {

	/**
	 * @var string
	 */
	static protected $extensionName = 'media';

	/**
	 * Return a folder
	 *
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	static public function get() {

		// Handle the file storage into the repository.
		$storageUid = (int) \TYPO3\CMS\Media\Utility\Configuration::get('storage');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);

		// Example identifier for $asset['target'] => "2:targetpath/targetfolder/"
		return $storageObject->getFolder(''); // get the root folder

	}

}
?>