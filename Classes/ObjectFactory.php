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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Domain\Model\Asset;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * Factory class for Media objects.
 */
class ObjectFactory implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $assetInstances = array();

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @return \TYPO3\CMS\Media\ObjectFactory
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\ObjectFactory');
	}

	/**
	 * Creates a media object from an array of file data. Requires a database
	 * row to be fetched.
	 *
	 * @param array $assetData
	 * @param string $objectType
	 * @throws \RuntimeException
	 * @return Asset
	 */
	public function createObject(array $assetData, $objectType = 'TYPO3\CMS\Media\Domain\Model\Asset') {

		if (!isset($assetData['storage']) && $assetData['storage'] === NULL) {
			throw new \RuntimeException('Storage identifier can not be null.', 1379947982);
		}

		$storage = ResourceFactory::getInstance()->getStorageObject($assetData['storage']);
		return GeneralUtility::makeInstance($objectType, $assetData, $storage);
	}

	/**
	 * Convert a content object into an asset and keep the instance for later use.
	 * Convenience method
	 *
	 * @param Content $object
	 * @return Asset
	 * @throws \RuntimeException
	 */
	public function convertContentObjectToAsset(Content $object) {

		if (empty($this->assetInstances[$object->getUid()])) {

			$assetData = $object->toArray();

			if (!isset($assetData['storage']) && $assetData['storage'] === NULL) {
				throw new \RuntimeException('Storage identifier can not be null.', 1379946981);
			}

			$storage = ResourceFactory::getInstance()->getStorageObject($assetData['storage']);

			/** @var Asset $asset */
			$asset = GeneralUtility::makeInstance('TYPO3\CMS\Media\Domain\Model\Asset', $assetData, $storage);
			$this->assetInstances[$asset->getUid()] = $asset;
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

		if ($identifier == NULL) {
			$storages = $this->getStorages();
			$storage = current($storages);
		} else {
			$storage = ResourceFactory::getInstance()->getStorageObject($identifier);
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

		$storages = $this->getBackendUser()->getFileStorages();
		if (empty($storages)) {
			throw new \RuntimeException('No storage is accessible for the current BE User. Forgotten to define a mount point for this BE User?', 1380801970);
		}
		return $storages;
	}

	/**
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
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
				$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('path', 'sys_filemounts', 'deleted = 0 AND uid = ' . $mountPointIdentifier);
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
	 * @param ResourceStorage $storage
	 * @param Asset $asset
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getTargetFolder($storage, $asset) {

		// default is the root level
		$folderObject = $storage->getRootLevelFolder();

		// Get a possible mount point for variant coming from the storage record.
		$storageRecord = $storage->getStorageRecord();
		$mountPointIdentifier = $storageRecord['mount_point_file_type_' . $asset->getType()];

		if ($mountPointIdentifier > 0) {

			// We don't have a Mount Point repository in FAL, so query the database directly.
			$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('path', 'sys_filemounts', 'deleted = 0 AND uid = ' . $mountPointIdentifier);
			if (!empty($record['path'])) {
				$folderObject = $storage->getFolder($record['path']);
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
	public function getVariantTargetFolder($storageIdentifier = NULL) {

		$storageObject = $this->getStorage($storageIdentifier);

		// default is the root level
		$folderObject = $storageObject->getRootLevelFolder();

		// Get a possible mount point for variant coming from the storage record.
		$storageRecord = $storageObject->getStorageRecord();
		$mountPointIdentifier = $storageRecord['mount_point_variant'];

		if ($mountPointIdentifier > 0) {

			// We don't have a Mount Point repository in FAL, so query the database directly.
			$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('path', 'sys_filemounts', 'deleted = 0 AND uid = ' . $mountPointIdentifier);
			if (!empty($record['path'])) {
				$folderObject = $storageObject->getFolder($record['path']);
			}
		}
		return $folderObject;
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}
