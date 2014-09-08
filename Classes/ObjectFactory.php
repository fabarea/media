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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * Factory class for Media objects.
 */
class ObjectFactory implements SingletonInterface {

	/**
	 * Gets a singleton instance of this class.
	 *
	 * @return \TYPO3\CMS\Media\ObjectFactory
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\ObjectFactory');
	}

	/**
	 * Convert a content object into a file object.
	 * @todo move me to an other class such as ConverterService
	 *
	 * @param Content $object
	 * @return File
	 * @throws \RuntimeException
	 */
	public function convertContentObjectToFile(Content $object) {

		$fileData = $object->toArray();

		if (!isset($fileData['storage']) && $fileData['storage'] === NULL) {
			throw new \RuntimeException('Storage identifier can not be null.', 1379946981);
		}

		$file = ResourceFactory::getInstance()->getFileObject($fileData['uid'], $fileData);

		return $file;
	}

	/**
	 * Return a folder object which contains an existing file or a file that has just been uploaded.
	 * @todo move me to an other class such as FolderService
	 *
	 * @param File|FileUpload\UploadedFileInterface $fileObject
	 * @param ResourceStorage $storage
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getContainingFolder($fileObject = NULL, ResourceStorage $storage) {

		// default is the root level
		$folderObject = $storage->getRootLevelFolder(); // get the root folder by default
		if ($fileObject instanceof File) {
			$folderObject = $storage->getFolder(dirname($fileObject->getIdentifier()));
		} elseif ($fileObject instanceof FileUpload\UploadedFileInterface) {

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
	 * @todo move me to an other class such as FolderService
	 *
	 * @param ResourceStorage $storage
	 * @param File $file
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getTargetFolder(ResourceStorage $storage, File $file) {

		// default is the root level
		$folderObject = $storage->getRootLevelFolder();

		// Retrieve storage record and a possible configured mount point.
		$storageRecord = $storage->getStorageRecord();
		$mountPointIdentifier = $storageRecord['mount_point_file_type_' . $file->getType()];

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

	/**
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
	}

}
