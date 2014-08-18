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
use TYPO3\CMS\Media\Utility\StorageUtility;
use TYPO3\CMS\Vidi\Persistence\Matcher;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class FilePermissionsAspect {

	/**
	 * Post process the matcher object.
	 *
	 * @param Matcher $matcher
	 * @param string $dataType
	 * @return void
	 */
	public function addFilePermissions(Matcher $matcher, $dataType) {
		if ($dataType === 'sys_file') {
			$this->respectStorage($matcher);

			if (FALSE === $this->getCurrentBackendUser()->isAdmin()) {
				$this->respectFilemounts($matcher);
			}
		}
	}

	/**
	 * @param Matcher $matcher
	 * @return void
	 */
	protected function respectStorage(Matcher $matcher) {
		$storage = StorageUtility::getInstance()->getCurrentStorage();

		// Set the storage identifier only if the storage is on-line.
		$identifier = -1;
		if ($storage->isOnline()) {
			$identifier = $storage->getUid();
		}

		$matcher->equals('storage', $identifier);
	}

	/**
	 * @param Matcher $matcher
	 * @return void
	 */
	protected function respectFileMounts(Matcher $matcher) {
		$matcher->setLogicalSeparatorForLike(Matcher::LOGICAL_OR);

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

		// Fetch the records
		$fileMountRecords = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'path',
			$tableName,
			$clause
		);

		foreach ($fileMountRecords as $fileMountRecord) {
			if ($fileMountRecord['path']) {
				$matcher->likes('identifier', $fileMountRecord['path'] . '%');
			}
		}
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

}
