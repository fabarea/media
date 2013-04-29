<?php
namespace TYPO3\CMS\Media\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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

/**
 * Repository for accessing categories
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class CategoryRepository extends \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository {

	/**
	 * Find related categories given a file uid
	 *
	 * @param int|object $file
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findRelated($file) {

		// note 1: FAL is not using the persistence layer of Extbase
		//         => annotation not possible @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<TYPO3\CMS\Extbase\Domain\Model\Category>
		// note 2: mm query is not implemented in Extbase
		//         => not possible $query = $this->createQuery();
		$sql = "SELECT * FROM sys_category AS category WHERE uid IN (SELECT uid_local FROM sys_category_record_mm WHERE uid_foreign = %s and tablenames = 'sys_file')";
		$statement = sprintf($sql,
			$this->getFileUid($file)
		);

		return $this->createQuery()->statement($statement)->execute();
	}

	/**
	 * Count related categories given a file uid.
	 *
	 * @param int|object $file
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function countRelated($file) {

		// note 1: FAL is not using the persistence layer of Extbase
		//         => annotation not possible @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<TYPO3\CMS\Extbase\Domain\Model\Category>
		// note 2: mm query is not implemented in Extbase
		//         => not possible $query = $this->createQuery();
		$sql = "SELECT count(*) AS count FROM sys_category AS category WHERE uid IN (SELECT uid_local FROM sys_category_record_mm WHERE uid_foreign = %s and tablenames = 'sys_file')";
		$statement = sprintf($sql,
			$this->getFileUid($file)
		);

		/** @var $databaseHandler \TYPO3\CMS\Core\Database\DatabaseConnection */
		$databaseHandler = $GLOBALS['TYPO3_DB'];
		$resource = $databaseHandler->sql_query($statement);
		$record = $databaseHandler->sql_fetch_assoc($resource);
		return $record['count'];
	}

	/**
	 * Get the file Uid out of mixed $file variable.
	 *
	 * @param int|object $file
	 * @throws \Exception
	 * @return int
	 */
	public function getFileUid($file) {

		// Make sure we can make something out of $file
		if (is_null($file)) {
			throw new \Exception('NULL value for variable $file', 1367240005);
		}

		// Fine the file uid.
		$fileUid = $file;
		if (is_object($file) && method_exists($file, 'getUid')) {
			if ($file->getUid() > 0) {
				$fileUid = $file->getUid();
			} else {
				$fileUid = 0;
			}
		}
		return $fileUid;
	}
}
?>