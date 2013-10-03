<?php
namespace TYPO3\CMS\Media;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Exception\StorageNotOnlineException;
use TYPO3\CMS\Media\Utility\ConfigurationUtility;

/**
 * Factory class for Media objects.
 */
class ObjectFactory implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @return \TYPO3\CMS\Media\ObjectFactory
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\\CMS\\Media\\ObjectFactory');
	}

	/**
	 * Creates a media object from an array of file data. Requires a database
	 * row to be fetched.
	 *
	 * @param array $fileData
	 * @param string $objectType
	 * @return object
	 */
	public function createObject(array $fileData, $objectType = 'TYPO3\CMS\Media\Domain\Model\Asset') {
		/** @var $object \TYPO3\CMS\Core\Resource\FileInterface */
		$object = GeneralUtility::makeInstance($objectType, $fileData);

		if (is_numeric($fileData['storage'])) {
			$resourceFactory = ResourceFactory::getInstance();
			$storageObject = $resourceFactory->getStorageObject($fileData['storage']);
			$object->setStorage($storageObject);
		}
		return $object;
	}

	protected $assetInstances = array();

	/**
	 * Convert a content object into an asset and keep the instance for later use.
	 * Convenience method
	 *
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @return \TYPO3\CMS\Media\Domain\Model\Asset
	 * @throws \RuntimeException
	 */
	public function convertContentObjectToAsset(\TYPO3\CMS\Vidi\Domain\Model\Content $object) {

		if (empty($this->assetInstances[$object->getUid()])) {

			/** @var \TYPO3\CMS\Media\Domain\Model\Asset $asset */
			$asset = GeneralUtility::makeInstance('TYPO3\CMS\Media\Domain\Model\Asset', $object->toArray());

			if ($object['storage'] === NULL) {
				throw new \RuntimeException('Preview rendering fails because storage property was null.', 1379946981);
			}

			if (is_numeric($object['storage']['uid'])) {
				$resourceFactory = ResourceFactory::getInstance();
				$storageObject = $resourceFactory->getStorageObject($object['storage']['uid']);
				$asset->setStorage($storageObject);
			}

			$this->assetInstances[$object->getUid()] = $asset;
		}

		return $this->assetInstances[$object->getUid()];
	}

	/**
	 * Return a storage object. If no identifier is given, return the default storage from the Media configuration.
	 * The returned storage object must be valid and on-line.
	 *
	 * @param int $identifier
	 * @throws \RuntimeException
	 * @throws Exception\StorageNotOnlineException
	 * @return ResourceStorage
	 */
	public function getStorage($identifier = NULL) {
		// If given storage is NULL, look into the configuration for getting a default value.
		if (is_null($identifier)) {
			$storageList = ConfigurationUtility::getInstance()->get('storages');
			$storages = GeneralUtility::trimExplode(',', $storageList);

			if (empty($storages)) {
				throw new \RuntimeException('No storage could be found. Check configuration in the Extension Manager', 1380793365);
			}
			$identifier = current($storages);
		}
		$storage = ResourceFactory::getInstance()->getStorageObject($identifier);

		// Check the storage is on-line
		if (!$storage->isOnline()) {
			$message = sprintf('The storage "%s" looks currently off-line. Check the storage configuration if you think this is an error', $storageObject->getName());
			throw new StorageNotOnlineException($message, 1361461834);
		}
		return $storage;
	}

	/**
	 * Return all storage objects under the control of Media. This option is configurable in the Extension Manager.
	 * The method also check storages are on-line.
	 *
	 * @throws \RuntimeException
	 * @return ResourceStorage[]
	 */
	public function getStorages() {

		$storages = array();
		$storageList = ConfigurationUtility::getInstance()->get('storages');
		if (strlen($storageList) === 0) {
			throw new \RuntimeException('No storage was selected in the Extension Manager. Check configuration there.', 1380801970);
		}
		$storageIdentifiers = GeneralUtility::trimExplode(',', $storageList);
		foreach ($storageIdentifiers as $storageIdentifier) {
			$storage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);
			if ($storage->isOnline()) {
				$storages[] = $storage;

			}
		}
		return $storages;
	}

	/**
	 * Return the current storage
	 *
	 * @return ResourceStorage
	 * @deprecated will be removed in media 1.2
	 */
	public function getCurrentStorage() {
		return $this->getStorage();
	}

	/**
	 * Return a folder object which contains an existing file or a file that has just been uploaded.
	 *
	 * @param \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\FileUpload\UploadedFileInterface  $fileObject
	 * @param int|NULL $storageIdentifier
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getContainingFolder($fileObject = NULL, $storageIdentifier = NULL) {

		$storageObject = $this->getStorage($storageIdentifier);

		// default is the root level
		$folderObject = $storageObject->getRootLevelFolder(); // get the root folder by default
		if ($fileObject instanceof \TYPO3\CMS\Core\Resource\File) {
			$folderObject = $storageObject->getFolder(dirname($fileObject->getIdentifier()));
		} elseif ($fileObject instanceof \TYPO3\CMS\Media\FileUpload\UploadedFileInterface) {

			// Get a possible mount point coming from the storage record.
			$storageRecord = $storageObject->getStorageRecord();
			$mountPointIdentifier = $storageRecord['mount_point_file_type_' . $fileObject->getType()];
			if ($mountPointIdentifier > 0) {

				// We don't have a Mount Point repository in FAL, so query the database directly.
				$record = $this->databaseHandler->exec_SELECTgetSingleRow('path', 'sys_filemounts', 'deleted = 0 AND uid = ' . $mountPointIdentifier);
				if (!empty($record['path'])) {
					$folderObject = $storageObject->getFolder($record['path']);
				}
			}
		}
		return $folderObject;
	}

	/**
	 * Return a folder object containing variant files.
	 *
	 * @param int|NULL $storageIdentifier
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getVariantFolder($storageIdentifier = NULL) {

		$storageObject = $this->getStorage($storageIdentifier);

		// default is the root level
		$folderObject = $storageObject->getRootLevelFolder();

		// Get a possible mount point for variant coming from the storage record.
		$storageRecord = $storageObject->getStorageRecord();
		$mountPointIdentifier = $storageRecord['mount_point_variant'];

		if ($mountPointIdentifier > 0) {

			// We don't have a Mount Point repository in FAL, so query the database directly.
			$record = $this->databaseHandler->exec_SELECTgetSingleRow('path', 'sys_filemounts', 'deleted = 0 AND uid = ' . $mountPointIdentifier);
			if (!empty($record['path'])) {
				$folderObject = $storageObject->getFolder($record['path']);
			}
		}
		return $folderObject;
	}
}


?>