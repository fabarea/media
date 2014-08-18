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
use TYPO3\CMS\Core\Resource\File;

/**
 * File Service.
 */
class FileService {

	/**
	 * @var string
	 */
	protected $tableName = 'sys_category';

	/**
	 * Find and return all categories for a file
	 *
	 * @param File $file
	 * @return array
	 */
	public function findCategories(File $file) {

		$metadataProperties = $file->_getMetaData();
		$clause = sprintf('uid IN (SELECT uid_local FROM sys_category_record_mm WHERE uid_foreign = %s and tablenames = "sys_file_metadata")', $metadataProperties['uid']);
		$clause .= $this->getWhereClauseForEnabledFields();

		$categories = $this->getDatabaseConnection()->exec_SELECTgetRows('*', $this->tableName, $clause);

		return $categories;
	}

	/**
	 * get the WHERE clause for the enabled fields of this TCA table
	 * depending on the context
	 *
	 * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
	 */
	protected function getWhereClauseForEnabledFields() {
		if ($this->isFrontendMode()) {
			// frontend context
			$whereClause = $this->getPageRepository()->enableFields($this->tableName);
			$whereClause .= $this->getPageRepository()->deleteClause($this->tableName);
		} else {
			// backend context
			$whereClause = BackendUtility::BEenableFields($this->tableName);
			$whereClause .= BackendUtility::deleteClause($this->tableName);
		}
		return $whereClause;
	}

	/**
	 * Returns whether the current mode is Frontend
	 *
	 * @return string
	 */
	protected function isFrontendMode() {
		return TYPO3_MODE == 'FE';
	}

	/**
	 * Returns a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Returns an instance of the page repository.
	 *
	 * @return \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	protected function getPageRepository() {
		return $GLOBALS['TSFE']->sys_page;
	}

}
