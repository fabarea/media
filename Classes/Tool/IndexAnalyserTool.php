<?php
namespace TYPO3\CMS\Media\Tool;

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
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\Tool\AbstractTool;

/**
 * Index analyser tool for the Media module.
 */
class IndexAnalyserTool extends AbstractTool {

	/**
	 * Display the title of the tool on the welcome screen.
	 *
	 * @return string
	 */
	public function getTitle() {
		return 'Analyse File index';
	}

	/**
	 * Display the description of the tool in the welcome screen.
	 *
	 * @return string
	 */
	public function getDescription() {
		$templateNameAndPath = 'EXT:media/Resources/Private/Backend/Standalone/Tool/IndexAnalyser/Launcher.html';
		$view = $this->initializeStandaloneView($templateNameAndPath);
		$view->assign('sitePath', PATH_site);
		return $view->render();
	}

	/**
	 * Do the job: analyse Index.
	 *
	 * @param array $arguments
	 * @return string
	 */
	public function work(array $arguments = array()) {

		// Possible clean up of missing files if the User has clicked so.
		if (!empty($arguments['deleteMissingFiles'])) {
			$this->deleteMissingFilesAction($arguments['files']);
		}

		$templateNameAndPath = 'EXT:media/Resources/Private/Backend/Standalone/Tool/IndexAnalyser/WorkResult.html';
		$view = $this->initializeStandaloneView($templateNameAndPath);

		$missingReports = array();
		$duplicateReports = array();
		foreach ($this->getStorageRepository()->findAll() as $storage) {
			$missingFiles = $this->getIndexAnalyser()->searchForMissingFiles($storage);

			$missingReports[] = array(
				'storage' => $storage,
				'missingFiles' => $missingFiles,
				'numberOfMissingFiles' => count($missingFiles),
			);

			$duplicateFiles = $this->getIndexAnalyser()->searchForDuplicatesFiles($storage);
			$duplicateReports[] = array(
				'storage' => $storage,
				'duplicateFiles' => $duplicateFiles,
				'numberOfDuplicateFiles' => count($duplicateFiles),
			);
		}

		$view->assign('missingReports', $missingReports);
		$view->assign('duplicateReports', $duplicateReports);

		return $view->render();
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
	protected function deleteMissingFilesAction(array $files = array()) {

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

	/**
	 * Tell whether the tools should be displayed according to the context.
	 *
	 * @return bool
	 */
	public function isShown() {
		return $this->getBackendUser()->isAdmin();
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

