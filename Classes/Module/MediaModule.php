<?php
namespace Fab\Media\Module;

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

use Fab\Media\FileUpload\UploadedFileInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for retrieving information about the Media module.
 */
class MediaModule implements SingletonInterface {

	/**
	 * Return the combined parameter from the URL.
	 *
	 * @return string
	 */
	public function getCombinedParameter() {

		// Fetch possible combined identifier.
		$combinedIdentifier = GeneralUtility::_GP('id');

		// Retrieve the default storage
		if (is_null($combinedIdentifier)) {
			$storage = $this->getStorageService()->findCurrentStorage();
			$combinedIdentifier = $storage->getUid() . ':/';
		}
		return urldecode($combinedIdentifier);
	}

	/**
	 * @param $combinedIdentifier
	 * @return Folder
	 */
	public function getFolderForCombinedIdentifier($combinedIdentifier) {

		// Code taken from FileListController.php
		$storage = ResourceFactory::getInstance()->getStorageObjectFromCombinedIdentifier($combinedIdentifier);
		$identifier = substr($combinedIdentifier, strpos($combinedIdentifier, ':') + 1);
		if (!$storage->hasFolder($identifier)) {
			$identifier = $storage->getFolderIdentifierFromFileIdentifier($identifier);
		}

		// Retrieve the folder object.
		$folder = ResourceFactory::getInstance()->getFolderObjectFromCombinedIdentifier($storage->getUid() . ':' . $identifier);

		// Disallow the rendering of the processing folder (e.g. could be called manually)
		// and all folders without any defined storage
		if ($folder && ($folder->getStorage()->getUid() == 0 || trim($folder->getStorage()->getProcessingFolder()->getIdentifier(), '/') === trim($folder->getIdentifier(), '/'))) {
			$storage = ResourceFactory::getInstance()->getStorageObjectFromCombinedIdentifier($combinedIdentifier);
			$folder = $storage->getRootLevelFolder();
		}

		return $folder;
	}

	/**
	 * Tell whether the Folder Tree is display or not.
	 *
	 * @return bool
	 */
	public function hasFolderTree() {
		$configuration = $this->getModuleConfiguration();
		return !(bool)$configuration['hide_folder_tree']['value'];
	}

	/**
	 * Tell whether the sub-folders must be included when browsing.
	 *
	 * @return bool
	 */
	public function hasRecursiveBrowsing() {

		$parameterPrefix = $this->getModuleLoader()->getParameterPrefix();
		$parameters = GeneralUtility::_GET($parameterPrefix);

		$hasRecursiveBrowsing = FALSE;
		if (isset($parameters['hasRecursiveBrowsing']) && $parameters['hasRecursiveBrowsing'] === 'true') {
			$hasRecursiveBrowsing = TRUE;
		}

		return $hasRecursiveBrowsing;
	}

	/**
	 * Return a folder object which contains an existing file or a file that has just been uploaded.
	 *
	 * @param File|UploadedFileInterface $fileObject
	 * @param ResourceStorage $storage
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	public function getContainingFolder($fileObject = NULL, ResourceStorage $storage) {

		// default is the root level
		$folderObject = $storage->getRootLevelFolder(); // get the root folder by default
		if ($fileObject instanceof File) {
			$folderObject = $storage->getFolder(dirname($fileObject->getIdentifier()));
		} elseif ($fileObject instanceof UploadedFileInterface) {

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
	 * @return array
	 */
	protected function getModuleConfiguration() {

		/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
		$configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
		return $configurationUtility->getCurrentConfiguration('media');
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

	/**
	 * @return \Fab\Media\Resource\StorageService
	 */
	protected function getStorageService() {
		return GeneralUtility::makeInstance('Fab\Media\Resource\StorageService');
	}

	/**
	 * @return MediaModule
	 */
	protected function getMediaModule() {
		return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
	}

	/**
	 * Return the module loader.
	 *
	 * @return \Fab\Vidi\Module\ModuleLoader
	 */
	public function getModuleLoader() {
		return GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader');
	}

}