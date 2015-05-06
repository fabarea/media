<?php
namespace Fab\Media\Resource;

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
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Utility\SessionUtility;

/**
 * Service for a Resource Storage.
 */
class StorageService implements SingletonInterface {

	/**
	 * @var ResourceStorage
	 */
	protected $currentStorage;

	/**
	 * Return all storage "attached" to a Backend User.
	 *
	 * @throws \RuntimeException
	 * @return ResourceStorage[]
	 */
	public function findByBackendUser() {

		$storages = $this->getBackendUser()->getFileStorages();
		if (empty($storages)) {
			throw new \RuntimeException('No storage is accessible for the current BE User. Forgotten to define a mount point for this BE User?', 1380801970);
		}
		return $storages;
	}

	/**
	 * Returns the current file storage in use.
	 *
	 * @return ResourceStorage
	 */
	public function findCurrentStorage() {
		if (is_null($this->currentStorage)) {

			$storageIdentifier = $this->getStorageIdentifierFromSessionOrArguments();

			if ($storageIdentifier > 0) {
				$currentStorage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);
			} else {

				// We differentiate the cases whether the User is admin or not.
				if ($this->getBackendUser()->isAdmin()) {

					$currentStorage = ResourceFactory::getInstance()->getDefaultStorage();

					// Not default storage has been flagged in "sys_file_storage".
					// Fallback approach: take the first storage as the current.
					if (!$currentStorage) {
						/** @var $storageRepository StorageRepository */
						$storageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository');

						$storages = $storageRepository->findAll();
						$currentStorage = current($storages);
					}
				} else {
					$fileMounts = $this->getBackendUser()->getFileMountRecords();
					$firstFileMount = current($fileMounts);
					$currentStorage = ResourceFactory::getInstance()->getStorageObject($firstFileMount['base']);
				}
			}

			$this->currentStorage = $currentStorage;
		}
		return $this->currentStorage;
	}

	/**
	 * Retrieve a possible storage identifier from the session or from the arguments.
	 *
	 * @return int
	 */
	public function getStorageIdentifierFromSessionOrArguments() {

		// Default value
		$storageIdentifier = 0;

		// Get last selected storage from User settings
		if (SessionUtility::getInstance()->get('lastSelectedStorage') > 0) {
			$storageIdentifier = SessionUtility::getInstance()->get('lastSelectedStorage');
		}

		$argumentPrefix = $this->getModuleLoader()->getParameterPrefix();
		$arguments = GeneralUtility::_GET($argumentPrefix);

		// Override selected storage from the session if GET argument "storage" is detected.
		if (!empty($arguments['storage']) && (int)$arguments['storage'] > 0) {
			$storageIdentifier = (int)$arguments['storage'];

			// Save state
			SessionUtility::getInstance()->set('lastSelectedStorage', $storageIdentifier);
		}

		return (int)$storageIdentifier;
	}

	/**
	 * Return the module loader.
	 *
	 * @return \Fab\Vidi\Module\ModuleLoader
	 */
	public function getModuleLoader() {
		return GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader');
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
