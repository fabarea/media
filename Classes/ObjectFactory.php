<?php
namespace TYPO3\CMS\Media;

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
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
	}

	/**
	 * Return a folder object which contains an existing file or a file that has just been uploaded.
	 *
	 * @param \TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Media\FileUpload\UploadedFileInterface  $fileObject
	 * @param ResourceStorage $storage
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getContainingFolder($fileObject = NULL, ResourceStorage $storage) {

		// default is the root level
		$folderObject = $storage->getRootLevelFolder(); // get the root folder by default
		if ($fileObject instanceof \TYPO3\CMS\Core\Resource\File) {
			$folderObject = $storage->getFolder(dirname($fileObject->getIdentifier()));
		} elseif ($fileObject instanceof \TYPO3\CMS\Media\FileUpload\UploadedFileInterface) {

			// Get a possible mount point coming from the storage record.
			$storageRecord = $storage->getStorageRecord();
			$mountPointIdentifier = $storageRecord['mount_point_file_type_' . $fileObject->getType()];
			if ($mountPointIdentifier > 0) {

				// We don't have a Mount Point repository in FAL, so query the database directly.
				$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('path', 'sys_filemounts', 'deleted = 0 AND uid = ' . $mountPointIdentifier);
				if (!empty($record['path'])) {
					$folderObject = $storage->getFolder($record['path']);
				}
			}
		}
		return $folderObject;
	}

	/**
	 * Return a folder object configured in the storage.
	 *
	 * @param ResourceStorage $storage
	 * @param Asset $asset
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getTargetFolder($storage, $asset) {

		// default is the root level
		$folderObject = $storage->getRootLevelFolder();

		// Retrieve storage record and a possible configured mount point.
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
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}
