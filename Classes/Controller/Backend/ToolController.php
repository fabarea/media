<?php
namespace TYPO3\CMS\Media\Controller\Backend;
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
use TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles tools related to Media.
 */
class ToolController extends ActionController {

	/**
	 * Initialize actions. These actions are meant to be called by an admin.
	 */
	public function initializeAction() {

		// This action is only allowed by Admin
		if (! $this->getBackendUser()->isAdmin()) {
			$message = 'Admin permission required.';
			throw new InsufficientUserPermissionsException($message, 1375952765);
		}
	}

	/**
	 * @return void
	 */
	public function welcomeAction() {
		$this->view->assign('sitePath', PATH_site);
	}

	/**
	 * @return void
	 */
	public function analyseIndexAction() {

		$missingReports = array();
		$duplicateReports = array();
		foreach ($this->getStorageService()->findAll() as $storage) {
			$missingFiles = $this->getIndexAnalyser()->searchForMissingFiles($storage);

			$missingReports[] = array(
				'storage' => $storage,
				'missingFiles' => $missingFiles,
				'numberOfMissingFiles' => count($missingFiles),
			);

			// @todo check me!
			$duplicateFiles = $this->getIndexAnalyser()->searchForDuplicatesFiles($storage);
			$duplicateReports[] = array(
				'storage' => $storage,
				'duplicateFiles' => $duplicateFiles,
				'numberOfDuplicateFiles' => count($duplicateFiles),
			);
		}

		$this->view->assign('missingReports', $missingReports);
		$this->view->assign('duplicateReports', $duplicateReports);
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
	public function deleteMissingFilesAction(array $files = array()) {

		foreach ($files as $file) {

			/** @var \TYPO3\CMS\Core\Resource\File $fileObject */
			try {
				$fileObject = ResourceFactory::getInstance()->getFileObject($file);
				if ($fileObject) {
					// The case is special as we have a missing file in the file system
					// As a result, we can't use $fileObject->delete(); which will
					// raise exception "Error while fetching permissions"
					$this->getDatabaseConnection()->exec_DELETEquery('sys_file', 'uid = ' . $fileObject->getUid());
				}
			}
			catch(\Exception $e) {
				continue;
			}
		}
		$this->redirect('analyseIndex');
	}

	/**
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Media\Index\IndexAnalyser
	 */
	protected function getIndexAnalyser() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Index\IndexAnalyser');
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\StorageService
	 */
	protected function getStorageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\StorageService');
	}

}
