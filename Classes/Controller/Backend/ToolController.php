<?php
namespace TYPO3\CMS\Media\Controller\Backend;

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

use TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
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
		foreach ($this->getStorageRepository()->findAll() as $storage) {
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
	 * @return StorageRepository
	 */
	protected function getStorageRepository() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
	}

}
