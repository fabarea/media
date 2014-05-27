<?php
namespace TYPO3\CMS\Media\SignalSlot;

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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Utility\StorageUtility;
use TYPO3\CMS\Vidi\Persistence\Matcher;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class ContentController {

	/**
	 * Post process the matcher object.
	 *
	 * @param Matcher $matcher
	 * @param string $dataType
	 * @return void
	 */
	public function postProcessMatcherObject(Matcher $matcher, $dataType) {
		if ($dataType === 'sys_file') {
			$this->respectStorage($matcher);

			if ($this->isBackendMode()) {
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
		if (FALSE === $this->getCurrentBackendUser()->isAdmin()) {
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
	}

	/**
	 * @return boolean
	 */
	protected function isBackendMode() {
		return TYPO3_MODE === 'BE';
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
