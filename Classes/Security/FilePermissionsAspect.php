<?php
namespace TYPO3\CMS\Media\Security;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\Persistence\Matcher;
use TYPO3\CMS\Vidi\Persistence\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class FilePermissionsAspect {

	/**
	 * Post-process the matcher object to respect the file storages.
	 *
	 * @param Matcher $matcher
	 * @param string $dataType
	 * @return void
	 */
	public function addFilePermissionsForFileStorages(Matcher $matcher, $dataType) {
		if ($dataType === 'sys_file') {
			$this->respectStorage($matcher);
		}
	}

	/**
	 * Post-process the constraints object to respect the file mounts.
	 *
	 * @param Query $query
	 * @param ConstraintInterface|NULL $constraints
	 * @return void
	 */
	public function addFilePermissionsForFileMounts(Query $query, $constraints) {
		if ($query->getType() === 'sys_file') {
			if (FALSE === $this->getCurrentBackendUser()->isAdmin()) {
				$this->respectFileMounts($query, $constraints);
			}
		}
	}

	/**
	 * @param Matcher $matcher
	 * @return void
	 */
	protected function respectStorage(Matcher $matcher) {
		$storage = $this->getStorageService()->findCurrentStorage();

		// Set the storage identifier only if the storage is on-line.
		$identifier = -1;
		if ($storage->isOnline()) {
			$identifier = $storage->getUid();
		}

		$matcher->equals('storage', $identifier);
	}

	/**
	 * @param Query $query
	 * @param ConstraintInterface|NULL $constraints
	 * @return array
	 */
	protected function respectFileMounts(Query $query, $constraints) {

		$tableName = 'sys_filemounts';

		// Get the file mount identifiers for the current Backend User.
		$fileMounts = GeneralUtility::trimExplode(',', $this->getCurrentBackendUser()->dataLists['filemount_list']);
		$fileMountUids = implode(',', array_filter($fileMounts));

		// Compute the clause.
		$clause = sprintf('uid IN (%s) %s %s',
			$fileMountUids,
			BackendUtility::BEenableFields($tableName),
			BackendUtility::deleteClause($tableName)
		);

		// Fetch the records.
		$fileMountRecords = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'path',
			$tableName,
			$clause
		);

		$constraintsRespectingFileMounts = array();
		foreach ($fileMountRecords as $fileMountRecord) {
			if ($fileMountRecord['path']) {
				$constraintsRespectingFileMounts[] = $query->like(
					'identifier',
					$fileMountRecord['path'] . '%'
				);
			}
		}
		$constraintsRespectingFileMounts = $query->logicalOr($constraintsRespectingFileMounts);

		$constraints = $query->logicalAnd(
			$constraints,
			$constraintsRespectingFileMounts
		);

		return array($query, $constraints);
	}

	/**
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getCurrentBackendUser() {
		return $GLOBALS['BE_USER'];
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\StorageService
	 */
	protected function getStorageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\StorageService');
	}
}
