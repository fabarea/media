<?php
namespace Fab\Media\Security;

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

use Fab\Media\Module\MediaModule;
use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Persistence\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class FilePermissionsAspect {

	/**
	 * Post-process the matcher object to respect the file storages.
	 *
	 * @param Matcher $matcher
	 * @param string $dataType
	 * @return void
	 */
	public function addFilePermissionsForFileStorages(Matcher $matcher, $dataType) {

		if ($dataType === 'sys_file' && $this->isPermissionNecessary()) {

			if ($this->isFolderConsidered()) {

				$folder = $this->getMediaModule()->getCurrentFolder();

				if ($this->getMediaModule()->hasRecursiveSelection()) {

					// Only add like condition if needed.
					if ($folder->getStorage()->getRootLevelFolder() !== $folder) {
						$matcher->like('identifier', $folder->getIdentifier() . '%', $automaticallyAddWildCard = FALSE);
					}
				}

				$matcher->equals('storage', $folder->getStorage()->getUid());
			} else {
				$storage = $this->getMediaModule()->getCurrentStorage();

				// Set the storage identifier only if the storage is on-line.
				$identifier = -1;
				if ($storage->isOnline()) {
					$identifier = $storage->getUid();
				}

				if ($this->getModuleLoader()->hasPlugin() && !$this->getCurrentBackendUser()->isAdmin()) {

					$fileMounts = $this->getCurrentBackendUser()->getFileMountRecords();
					$collectedFiles = array();
					foreach ($fileMounts as $fileMount) {

						$combinedIdentifier = $fileMount['base'] . ':' . $fileMount['path'];
						$folder = ResourceFactory::getInstance()->getFolderObjectFromCombinedIdentifier($combinedIdentifier);

						$files = $this->getFileUids($folder);
						$collectedFiles = array_merge($collectedFiles, $files);
					}

					$matcher->in('uid', $collectedFiles);
				}

				$matcher->equals('storage', $identifier);
			}
		}
	}

	/**
	 * @return bool
	 */
	protected function isPermissionNecessary() {

		$isNecessary = TRUE;

		$parameters = GeneralUtility::_GET(VidiModule::getParameterPrefix());

		if ($parameters['controller'] === 'Clipboard'  && ($parameters['action'] === 'show' || $parameters['action'] === 'flush')) {
			$isNecessary = FALSE;
		}

		if ($parameters['controller'] === 'Content' && ($parameters['action'] === 'copyClipboard' || $parameters['action'] === 'moveClipboard')) {
			$isNecessary = FALSE;
		}

		return $isNecessary;
	}

	/**
	 * @return bool
	 */
	protected function isFolderConsidered() {
		return $this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin();
	}

	/**
	 * @param Folder $folder
	 * @return array
	 */
	protected function getFileUids(Folder $folder) {
		$files = array();
		foreach ($folder->getFiles() as $file) {
			$files[] = $file->getUid();
		}
		return $files;
	}

	/**
	 * Post-process the constraints object to respect the file mounts.
	 *
	 * @param Query $query
	 * @param ConstraintInterface|NULL $constraints
	 * @return void
	 */
	public function addFilePermissionsForFileMounts(Query $query, $constraints) {
		if ($query->getType() === 'sys_file') {
			if (!$this->getCurrentBackendUser()->isAdmin()) {
				$this->respectFileMounts($query, $constraints);
			}
		}
	}

	/**
	 * @param Query $query
	 * @param ConstraintInterface|NULL $constraints
	 * @return array
	 */
	protected function respectFileMounts(Query $query, $constraints) {

		$tableName = 'sys_filemounts';

		// Get the file mount identifiers for the current Backend User.
		$fileMounts = GeneralUtility::trimExplode(',', $this->getCurrentBackendUser()->dataLists['filemount_list']);
		$fileMountUids = implode(',', array_filter($fileMounts));

		// Compute the clause.
		$clause = sprintf('uid IN (%s) %s %s',
			$fileMountUids,
			BackendUtility::BEenableFields($tableName),
			BackendUtility::deleteClause($tableName)
		);

		// Fetch the records.
		$fileMountRecords = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'path',
			$tableName,
			$clause
		);

		$constraintsRespectingFileMounts = array();
		foreach ($fileMountRecords as $fileMountRecord) {
			if ($fileMountRecord['path']) {
				$constraintsRespectingFileMounts[] = $query->like(
					'identifier',
					$fileMountRecord['path'] . '%'
				);
			}
		}
		$constraintsRespectingFileMounts = $query->logicalOr($constraintsRespectingFileMounts);

		$constraints = $query->logicalAnd(
			$constraints,
			$constraintsRespectingFileMounts
		);

		return array($query, $constraints);
	}

	/**
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getCurrentBackendUser() {
		return $GLOBALS['BE_USER'];
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @return MediaModule
	 */
	protected function getMediaModule() {
		return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
	}

	/**
	 * Get the Vidi Module Loader.
	 *
	 * @return \Fab\Vidi\Module\ModuleLoader
	 */
	protected function getModuleLoader() {
		return GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader');
	}
}
