<?php
namespace TYPO3\CMS\Media\Service;

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
 * A class providing indexing service for Media
 *
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class AssetIndexerService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * Index all files from the Media Storage. The method returns
	 * an array containing the name of the storage and the number
	 * of files found (including the new indexed files) in the media storage.
	 *
	 * @return array
	 */
	public function indexStorage(){

		$storage = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorage();
		$folder = $storage->getRootLevelFolder();

		/** @var \TYPO3\CMS\Core\Resource\Service\IndexerService $indexerService */
		$indexerService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');
		$numberOfFiles = $indexerService->indexFilesInFolder($folder);

		$result = array(
			'storageName' => $storage->getName(),
			'fileNumber' => $numberOfFiles,
		);
		return $result;
	}

	/**
	 * Return missing file in the storage
	 *
	 * @return array
	 */
	public function getMissingResources() {
		/** @var $assetRepository \TYPO3\CMS\Media\Domain\Repository\AssetRepository */
		$assetRepository = $this->objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');

		$missingFiles = array();
		foreach ($assetRepository->findAll() as $asset) {
			if (!$asset->exists()) {
				$missingFiles[] = $asset;
			}
		}
		return $missingFiles;
	}

	/**
	 * Return duplicates file records
	 *
	 * @return array
	 */
	public function getDuplicates() {

		// Detect duplicate records
		$resource = $this->databaseHandler->sql_query('SELECT identifier FROM sys_file WHERE deleted = 0 AND sys_language_uid = 0 GROUP BY identifier, storage Having COUNT(*) > 1');
		$duplicates = array();
		while ($row = $this->databaseHandler->sql_fetch_assoc($resource)) {
			$records = $this->databaseHandler->exec_SELECTgetRows('uid', 'sys_file', sprintf('deleted = 0 AND identifier = "%s"', $row['identifier']));
			$duplicates[$row['identifier']] = $records;
		}
		return $duplicates;
	}
}
?>