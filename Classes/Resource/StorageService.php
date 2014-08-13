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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Service for a Resource Storage.
 */
class StorageService implements SingletonInterface {

	/**
	 * Find all Resource Storages
	 *
	 * @return ResourceStorage[]
	 */
	public function findAll() {
		$storages = array();

		$tableName = 'sys_file_storage';
		$clause = '1 = 1';
		$clause .= BackendUtility::BEenableFields($tableName);
		$clause .= BackendUtility::deleteClause($tableName);

		$records = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', 'sys_file_storage', $clause);

		foreach ($records as $record) {
			$storages[] = ResourceFactory::getInstance()->getStorageObject($record['uid']);
		}

		return $storages;
	}

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
