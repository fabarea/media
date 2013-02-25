<?php
namespace TYPO3\CMS\Media\Controller;
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
 * Controller which handles the migration from DAM
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class ToolController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	/**
	 * @var string
	 */
	protected $importedFieldPrefix = '_dam_imported_';

	/**
	 * @throws \TYPO3\CMS\Media\Exception\StorageNotOnlineException
	 */
	public function initializeAction() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$storageUid = (int) \TYPO3\CMS\Media\Utility\Configuration::get('storage');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);
		$this->view->assign('publicPath', $storageObject->getRootLevelFolder()->getPublicUrl());
	}

	/**
	 * @return void
	 */
	public function checkStatusAction() {

		$this->databaseHandler->exec_SELECTgetRows('*', 'sys_file', 'deleted = 0');

		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var $assetRepository \TYPO3\CMS\Media\Domain\Repository\AssetRepository */
		$assetRepository = $objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');

		$missingFiles = array();
		foreach ($assetRepository->findAll() as $asset) {
			if (!$asset->exists()) {
				$missingFiles[] = $asset;
			}
		}

		$this->view->assign('missingFiles', $missingFiles);

		// Detect duplicate records
		$resource = $this->databaseHandler->sql_query('SELECT identifier FROM sys_file WHERE deleted = 0 GROUP By identifier, storage Having COUNT(*) > 1');
		$duplicatedIdentifiers = array();
		while($row = $this->databaseHandler->sql_fetch_assoc($resource)) {
			$records = $this->databaseHandler->exec_SELECTgetRows('uid', 'sys_file', sprintf('deleted = 0 AND identifier = "%s"', $row['identifier']));
			$duplicatedIdentifiers[$row['identifier']] = $records;
		}
		$this->view->assign('duplicatedIdentifiers', $duplicatedIdentifiers);
		$this->view->assign('everythingOk', empty($missingFiles) && empty($duplicatedIdentifiers));
	}
}
?>
