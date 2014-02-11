<?php
namespace TYPO3\CMS\Media\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Repository for accessing categories
 */
class CategoryRepository extends \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository {

	/**
	 * @var string
	 */
	protected $tableName = 'sys_category';

	/**
	 * Find related categories given a file uid.
	 *
	 * note 1: FAL is not using the persistence layer of Extbase
	 * => annotation not possible @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<TYPO3\CMS\Extbase\Domain\Model\Category>
	 *
	 * note 2: mm query is not implemented in Extbase
	 * => not possible $query = $this->createQuery();
	 *
	 * @param File $file
	 * @return ObjectStorage
	 */
	public function findRelated(File $file) {
		$metadataProperties = $file->_getMetaData();
		$clause = sprintf('uid IN (SELECT uid_local FROM sys_category_record_mm WHERE uid_foreign = %s and tablenames = "sys_file_metadata")', $metadataProperties['uid']);
		$clause .= $this->getWhereClauseForEnabledFields();

		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows('*', $this->tableName, $clause);

		$objectStorage = new ObjectStorage();
		foreach ($rows as $row) {
			/** @var \TYPO3\CMS\Extbase\Domain\Model\Category $category */
			$category = $this->objectManager->get('TYPO3\CMS\Extbase\Domain\Model\Category');

			foreach ($row as $fieldName => $value) {
				$propertyName = GeneralUtility::underscoredToLowerCamelCase($fieldName);
				$category->_setProperty($propertyName, $value);
			}
			$objectStorage->attach($category);
		}

		return $objectStorage;
	}

	/**
	 * Count related categories given a File.
	 *
	 * @param File $file
	 * @return int
	 */
	public function countRelated(File $file) {
		return $file->getProperty('categories');
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
			$whereClause = \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields($this->tableName);
			$whereClause .= \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause($this->tableName);
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
