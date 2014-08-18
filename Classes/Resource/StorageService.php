<?php
namespace TYPO3\CMS\Media\Resource;

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
use TYPO3\CMS\Media\Utility\SessionUtility;

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

			$parameterPrefix = $this->getModuleLoader()->getParameterPrefix();
			$parameters = GeneralUtility::_GET($parameterPrefix);

			// Default value
			$identifierParameter = NULL;

			// Get last selected storage from User settings
			if (SessionUtility::getInstance()->get('lastSelectedStorage') > 0) {
				$identifierParameter = SessionUtility::getInstance()->get('lastSelectedStorage');
			}

			// Override selected storage if get parameter is seen.
			if (!empty($parameters['storage']) && (int) $parameters['storage'] > 0) {
				$identifierParameter = (int) $parameters['storage'];

				// Save state
				SessionUtility::getInstance()->set('lastSelectedStorage', $identifierParameter);
			}

			if ($identifierParameter > 0) {
				$this->currentStorage = ResourceFactory::getInstance()->getStorageObject($identifierParameter);
			} else {
				$this->currentStorage = ResourceFactory::getInstance()->getDefaultStorage();
			}
		}
		return $this->currentStorage;
	}

	/**
	 * Return the module loader.
	 *
	 * @return \TYPO3\CMS\Vidi\Module\ModuleLoader
	 */
	public function getModuleLoader() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Module\ModuleLoader');
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
