<?php
namespace TYPO3\CMS\Media\Controller;
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

/**
 * Controller which handles the migration from DAM
 */
class ToolController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	/**
	 * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected $backendUser;

	/**
	 * @var \TYPO3\CMS\Media\Service\AssetIndexerService
	 */
	protected $assetIndexerService;

	/**
	 * @var string
	 */
	protected $importedFieldPrefix = '_dam_imported_';

	/**
	 * Initialize actions. These actions are meant to be called by an admin.
	 */
	public function initializeAction() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
		$this->backendUser = $GLOBALS['BE_USER'];

		// This action is only allowed by Admin
		if (! $this->backendUser->isAdmin()) {
			$message = 'Admin permission required.';
			throw new \TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException($message, 1375952765);
		}
		$this->assetIndexerService = $this->objectManager->get('TYPO3\CMS\Media\Service\AssetIndexerService');
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$storageUid = (int) \TYPO3\CMS\Media\Utility\ConfigurationUtility::getInstance()->get('storages');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);
		$this->view->assign('publicPath', $storageObject->getRootLevelFolder()->getPublicUrl());
		$this->view->assign('sitePath', PATH_site);
	}

	/**
	 * @return void
	 */
	public function checkIndexAction() {

		$missingResources = $this->assetIndexerService->getMissingResources();
		$duplicates = $this->assetIndexerService->getDuplicates();

		$this->view->assign('missingResources', $missingResources);
		$this->view->assign('duplicates', $duplicates);
		$this->view->assign('everythingOk', empty($missingResources) && empty($duplicates));
	}

	/**
	 * Delete files given as parameter.
	 * This is a special case as we have a missing file in the file system
	 * As a result, we can't use $fileObject->delete(); which will
	 * raise exception "Error while fetching permissions".
	 *
	 * @param array $files
	 * @return void
	 */
	public function deleteFilesAction(array $files = array()) {

		/** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
		$fileRepository = $this->objectManager->get('TYPO3\CMS\Core\Resource\FileRepository');

		foreach ($files as $file) {

			/** @var \TYPO3\CMS\Core\Resource\File $fileObject */
			try {
				$fileObject = $fileRepository->findByUid($file);
				if ($fileObject) {
					// The case is special as we have a missing file in the file system
					// As a result, we can't use $fileObject->delete(); which will
					// raise exception "Error while fetching permissions"
					$this->databaseHandler->exec_UPDATEquery('sys_file', 'uid = ' . $fileObject->getUid(), array('deleted' => 1));
				}
			}
			catch(\Exception $e) {
				continue;
			}
		}
		$this->redirect('checkIndex');
	}
}
?>
