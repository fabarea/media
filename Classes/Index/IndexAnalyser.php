<?php
namespace TYPO3\CMS\Media\Index;

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

/**
 * A class providing indexing service for Media/
 */
class IndexAnalyser implements SingletonInterface {

	/**
	 * Return missing file for a given storage.
	 *
	 * @param ResourceStorage $storage
	 * @return array
	 */
	public function searchForMissingFiles(ResourceStorage $storage) {

		$query = $this->getDatabaseConnection()->SELECTquery('*', 'sys_file', 'storage = ' . $storage->getUid());
		$resource = $this->getDatabaseConnection()->sql_query($query);

		$missingFiles = array();
		while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($resource)) {

			// This task is very memory consuming on large data set e.g > 20'000 records.
			// We must think of having a pagination if there is the need for such thing.
			$file = ResourceFactory::getInstance()->getFileObject($row['uid'], $row);
			if (!$file->exists()) {
				$missingFiles[] = $file;
			}
		}
		return $missingFiles;
	}

	/**
	 * Return duplicates file records
	 *
	 * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
	 * @return array
	 */
	public function searchForDuplicatesFiles(ResourceStorage $storage) {

		// Detect duplicate records.
		$query = "SELECT identifier FROM sys_file WHERE storage = {$storage->getUid()} GROUP BY identifier, storage Having COUNT(*) > 1";
		$resource = $this->getDatabaseConnection()->sql_query($query);
		$duplicates = array();
		while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($resource)) {
			$records = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', 'sys_file', sprintf('identifier = "%s"', $row['identifier']));
			$duplicates[$row['identifier']] = $records;
		}
		return $duplicates;
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}
