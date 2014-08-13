<?php
namespace TYPO3\CMS\Media\Index;

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
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * A class providing indexing service for Media/
 */
class IndexAnalyser implements SingletonInterface {

	/**
	 * Index all files from the Media Storage. The method returns
	 * an array containing the name of the storage and the number
	 * of files found (including the new indexed files) in the media storage.
	 *
	 * @return array
	 */
	public function indexStorage(){

		$storage = ObjectFactory::getInstance()->getStorage();
		$folder = $storage->getRootLevelFolder();

		/** @var \TYPO3\CMS\Core\Resource\Service\IndexerService $indexerService */
		$indexerService = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
		$numberOfFiles = $indexerService->indexFilesInFolder($folder);

		$result = array(
			'storageName' => $storage->getName(),
			'fileNumber' => $numberOfFiles,
		);
		return $result;
	}

	/**
	 * Return missing file for a given storage.
	 *
	 * @param ResourceStorage $storage
	 * @return array
	 */
	public function searchForMissingFiles(ResourceStorage $storage) {
		$fileRecords = $this->getDatabaseConnection()->exec_SELECTgetRows('*', 'sys_file', 'storage = ' . $storage->getUid());

		$missingFiles = array();
		foreach ($fileRecords as $fileRecord) {
			$file = ResourceFactory::getInstance()->getFileObject($fileRecord['uid'], $fileRecord);
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
