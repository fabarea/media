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
 * @deprecated
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MigrationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

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
		$storageUid = (int) \TYPO3\CMS\Media\Utility\Setting::getInstance()->get('storage');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);
		if (!$storageObject->isOnline()) {
			$message = sprintf('The storage "%s" looks currently off-line. Check the storage configuration if you think this is an error', $storageObject->getName());
			throw new \TYPO3\CMS\Media\Exception\StorageNotOnlineException($message, 1361461834);
		}

		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$storageUid = (int) \TYPO3\CMS\Media\Utility\Setting::getInstance()->get('storage');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);
		$this->view->assign('publicPath', $storageObject->getRootLevelFolder()->getPublicUrl());
	}

	/**
	 * @return void
	 */
	public function resetAction() {

		// Fetch existing field from tables ``sys_file`` and ``tx_dam``
		$fieldsInSysFile = $this->databaseHandler->admin_get_fields('sys_file');

		if (isset($fieldsInSysFile['imported_from_dam'])) {
			// Remove all imported record
			$this->databaseHandler->sql_query('DELETE FROM sys_file WHERE imported_from_dam = 1');
		}

		// remove old fields involved in the migration
		foreach ($fieldsInSysFile as $fieldName => $fieldInfo) {
			$expression = sprintf('/^%s/isU', $this->importedFieldPrefix);
			if (preg_match($expression, $fieldName)) {
				$this->databaseHandler->sql_query('ALTER TABLE sys_file DROP COLUMN ' . $fieldName);
			}
		}
		$this->databaseHandler->sql_query('ALTER TABLE sys_file DROP COLUMN imported_from_dam');
	}

	/**
	 * Action migrate from DAM.
	 *
	 * @return void
	 */
	public function migrateAction() {

		$excludedFields = array(
			'uid',
			'type',
			'parent_id',
			'active',
			'sorting',
			'fe_group',
			'sys_language_uid',
			'l10n_parent',
			'l10n_diffsource',
			'file_mime_subtype',
			'file_type_version',
			'file_inode',
			'imported_from_dam',
		);

		$mappingFields = array(
			'media_type' => 'type',
			'file_mime_type' => 'mime_type',
			'file_type' => 'extension',
			'file_name' => 'name',
			'file_path' => 'identifier',
			'file_size' => 'size',
			'file_mtime' => 'modification_date',
			'file_ctime' => 'creation_date',
			'file_hash' => 'sha1',
			'file_status' => 'status',
			'highlight' => 'ranking',
			'nb_pages' => 'pages',
			'file_dl_name' => 'download_name',
			'loc_region' => 'location_region',
			'loc_country' => 'location_country',
			'loc_city' => 'location_city',
			'published_user_id' => 'publisher',
			'comment' => 'note',
		);

		// Get the storage object
		$storageUid = (int) \TYPO3\CMS\Media\Utility\Setting::getInstance()->get('storage');
		$storageObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getStorageObject($storageUid);

		// Fetch existing field from tables ``sys_file`` and ``tx_dam``
		$fieldsInSysFile = $this->databaseHandler->admin_get_fields('sys_file');
		$fieldsInTxDam = $this->databaseHandler->admin_get_fields('tx_dam');

		// Create a new column for telling apart imported records
		if (empty($fieldsInSysFile['imported_from_dam'])) {
			$this->databaseHandler->sql_query('ALTER TABLE sys_file ADD COLUMN imported_from_dam TINYINT(1)');
		}

		// Remove all imported record
		$this->databaseHandler->sql_query('DELETE FROM sys_file WHERE imported_from_dam = 1');

		$missingFiles = $newFields = array();

		// Fetch all record from tx_dam
		$records = $this->databaseHandler->exec_SELECTgetRows('*', 'tx_dam', 'deleted = 0');
		foreach ($records as $record) {

			$filePathAndName = sprintf('%s%s/%s',
				PATH_site,
				trim($record['file_path'], '/'),
				$record['file_name']
			);
			if (file_exists($filePathAndName)) {

				// Start a new record
				$_record = array();

				foreach ($record as $fieldName => $value) {

					if (!in_array($fieldName, $excludedFields)) {

						// Check if there is a mapping info
						if (!empty($mappingFields[$fieldName])) {
							$fieldName = $mappingFields[$fieldName];

							// special case for identifier
							if ($fieldName == 'identifier') {
								$provisoryIdentifier = '/' . trim($value, '/') . '/' . $record['file_name'];
								// removes the base path from the identifier
								$value = str_replace($storageObject->getRootLevelFolder()->getPublicUrl(), '', $provisoryIdentifier);
							}
						}

						if (isset($fieldsInSysFile[$fieldName])) {
							$_record[$fieldName] = $value;
						} else {
							$importedFieldName = $this->importedFieldPrefix . $fieldsInTxDam[$fieldName]['Field'];

							if (!isset($fieldsInSysFile[$importedFieldName])) {

								// create a new field if it was not found
								$request = sprintf('ALTER TABLE sys_file ADD COLUMN  %s %s',
									$importedFieldName,
									$fieldsInTxDam[$fieldName]['Type']
								);
								$this->databaseHandler->sql_query($request);
								$fieldsInSysFile[$importedFieldName] = array();

							}

							$_record[$importedFieldName] = $value;
							$newFields[$importedFieldName] = $importedFieldName;
						}
					}
				}

				$_record['imported_from_dam'] = '1';
				$_record['storage'] = \TYPO3\CMS\Media\Utility\Setting::getInstance()->get('storage');

				$this->databaseHandler->exec_INSERTquery('sys_file', $_record);
			} else {
				$missingFiles[] = $filePathAndName;
			}
		}

		// Clean up request - Media does not support more than type > 5
		$this->databaseHandler->sql_query('UPDATE sys_file SET type = 5 WHERE type > 5 AND imported_from_dam = 1');

		// Adjust pid
		$defaultPid = \TYPO3\CMS\Media\Utility\MediaFolder::getDefaultPid();
		$this->databaseHandler->sql_query('UPDATE sys_file SET pid = ' . $defaultPid . ' WHERE imported_from_dam = 1');

		// Update the metadata
		$this->updateIndexAction();

		$this->view->assign('newFields', $newFields);
		$this->view->assign('mappingFields', $mappingFields);
		$this->view->assign('defaultPid', $defaultPid);
		$this->view->assign('missingFiles', $missingFiles);

		// @todo importation is not finished, check out http://forge.typo3.org/issues/30711
	}

	/**
	 * Update the metadata of every file.
	 *
	 * @return boolean
	 */
	public function updateIndexAction(){

		// Call the indexer service for updating the metadata of each file.
		/** @var $indexerService \TYPO3\CMS\Core\Resource\Service\IndexerService */
		$indexerService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Service\IndexerService');

		/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
		$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
		$files = $fileRepository->findAll();

		foreach ($files as $file) {
			/** @var $file \TYPO3\CMS\Core\Resource\File */
			if ($file->exists()) {
				$indexerService->indexFile($file, TRUE);
			}
		}

		return TRUE;
	}
}
?>
