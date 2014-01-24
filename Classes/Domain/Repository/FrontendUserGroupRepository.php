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
 * Repository for accessing Frontend User Group.
 */
class FrontendUserGroupRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository {

	/**
	 * @var string
	 */
	protected $tableName = 'fe_groups';

	/**
	 * Find related Frontend User Groups given a File.
	 *
	 * @param File $file
	 * @return ObjectStorage
	 */
	public function findRelated(File $file) {

		$frontendGroupList = implode(',', $this->getFrontendGroupIdentifiers($file));

		$clause = sprintf('uid IN (%s)', $frontendGroupList);
		$clause .= $this->getWhereClauseForEnabledFields();

		$rows = $this->getDatabaseConnection()->exec_SELECTgetRows('*', $this->tableName, $clause);

		$objectStorage = new ObjectStorage();
		foreach ($rows as $row) {
			/** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $frontendUserGroup */
			$frontendUserGroup = $this->objectManager->get('TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup');

			foreach ($row as $fieldName => $value) {
				$propertyName = GeneralUtility::underscoredToLowerCamelCase($fieldName);
				$frontendUserGroup->_setProperty($propertyName, $value);
			}
			$objectStorage->attach($frontendUserGroup);
		}

		return $objectStorage;
	}

	/**
	 * Count related Frontend User Groups related to the File.
	 *
	 * @param File $file
	 * @return int
	 */
	public function countRelated(File $file) {
		return count($this->getFrontendGroupIdentifiers($file));
	}

	/**
	 * Returns the Frontend Group Identifier from a given File
	 *
	 * @param File $file
	 * @return array
	 */
	protected function getFrontendGroupIdentifiers(File $file) {
		$frontendGroupCsv = $file->getProperty('fe_groups');
		return GeneralUtility::trimExplode(',', $frontendGroupCsv, TRUE);
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
?>