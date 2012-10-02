<?php

/* * *************************************************************
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
 * ************************************************************* */

/**
 * TCE hook handling
 *
 * @package     TYPO3
 * @subpackage  speciality
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version $Id: class.tx_speciality_tcehook.php 535 2010-10-12 10:19:30Z fudriot $
 */
class Tx_Media_Hooks_TCE {

	/**
	 * The extension key
	 *
	 * @var string
	 */
	protected $extKey = 'media';

	/**
	 * The name of the table
	 *
	 * @var string
	 */
	protected $tableName = 'sys_file';

	/**
	 * @var t3lib_file_Factory
	 */
	protected $factory;

	/**
	 * @var t3lib_file_Domain_Repository_MountRepository
	 */
	protected $mountRepository;

	/**
	 * @var t3lib_file_Domain_Model_Mount
	 */
	protected $mount;

	/**
	 * Is a child of t3lib_file_Service_Storage_AbstractDriver
	 *
	 * @var object
	 */
	protected $driver;

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * @return void
	 */
	protected function initializeAction() {

		// Load preferences
		if ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]) {
			$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		}

		// Instantiate necessary stuff for FAL
		$this->factory = t3lib_div::makeInstance('t3lib_file_Factory');
		$this->mountRepository = t3lib_div::makeInstance('t3lib_file_Domain_Repository_MountRepository');
		$this->mount = $this->mountRepository->findByUid($this->configuration['storage']);
		$this->driver = $this->mount->getDriver();
	}

	/**
	 * delete file when record is deleted
	 */
//	function processCmdmap_preProcess($command, $table, $id, $value, $tce) {
//	}

	/**
	 * status TXMedia Management_status_file_changed will be reset when record was edited
	 *
	 * @param	string		action status: new/update is relevant for us
	 * @param	string		db table
	 * @param	integer		record uid
	 * @param	array		record
	 * @param	object		parent object
	 * @return	void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj) {
		// TRUE means a file has been uploaded
		if ($table === $this->tableName && !empty($pObj->uploadedFileArray[$this->tableName]['_userfuncFile']['file']['name'])) {
			$uploadedFile = array();

			// Init action
			$this->initializeAction();

			// @todo check if file must be overwritten
			// @todo fetch this config from TypoScript or so...
			if (TRUE && $status == 'update') {
				$mediaRepository = t3lib_div::makeInstance('Tx_Media_Domain_Repository_MediaRepository');
				$media = $mediaRepository->findByUid($id);

				$previousFileName = $this->getPreviousFileName($media);
				if ($previousFileName) {
					$pObj->uploadedFileArray[$this->tableName]['_userfuncFile']['file']['name'] = $previousFileName;
				}
			}


			$uploadedFile = $pObj->uploadedFileArray[$this->tableName]['_userfuncFile']['file'];
			$file = $this->upload($uploadedFile);

				// Update the record with data coming from the file
			$fieldArray['name'] = $file->getName();
			$fieldArray['size'] = $file->getSize();
			$fieldArray['identifier'] = $file->getIdentifier();
			$fieldArray['type'] = $file->getType();
			$fieldArray['mime_type'] = $file->getMimeType();
			$fieldArray['sha1'] = $file->getSha1();

				// Init service
			$metadataService = t3lib_div::makeInstance('Tx_Media_Service_Metadata');

				// $metaDataArray is an array with indexes equivalent to fields in Tx_Media_Model_Media
			$metadata = $metadataService->getMetadata($file);

			if (!empty($metadata)) {

				// @todo fetch this config from sys_file_indexingConfiguration
				$columns = $this->getColumns($this->tableName);
				foreach ($columns as $columnName => $column) {
					// check whether the value must be filled in from the metadata extraction
					// condition: a value has been fetched + the User has access to the field
					if (!empty($metadata[$columnName]) &&
							$this->hasPermission($columnName, $column, $this->tableName)) {
						$fieldArray[$columnName] = $metadata[$columnName];
					}
				}
			}

			// create a thumbnail for the first time
			if ($status == 'new') {
				$thumbnailService = t3lib_div::makeInstance('Tx_Media_Service_Thumbnail');
				$thumbnailFile = $thumbnailService->createThumbnailFile($file, $this->mount);
				$thumbnailFile = $this->index($thumbnailFile);
				$fieldArray['thumbnail'] = $thumbnailFile->getUid();
			}
		}
	}

	/**
	 * Returns the columns of a table
	 *
	 * @return array
	 */
	protected function getColumns($tableName) {
		t3lib_div::loadTCA($tableName);
		return $GLOBALS['TCA'][$tableName]['columns'];
	}

	/**
	 * Check whether the BE User has accedd to the field.
	 *
	 * @param string $fieldName the field name to be checked agains the permission
	 * @param array $fieldConfiguration the TCA of the field
	 * @param string $tableName the table name
	 * @return boolean
	 */
	protected function hasPermission($fieldName, $fieldConfiguration, $tableName) {
		$hasPermission = FALSE;
		if ($GLOBALS['BE_USER']->isAdmin() ||
				(isset($columns[$fieldName]['exclude']) && !$fieldConfiguration['exclude']) ||
				$GLOBALS['BE_USER']->check('non_exclude_fields', $tableName . ':' . $fieldName)) {
			$hasPermission = TRUE;
		}
		return $hasPermission;
	}

	/**
	 * Returns the previous file name of the file
	 *
	 * @param Tx_Media_Model_Media a $media
	 */
	protected function getPreviousFileName($media) {
		if ($media->getFile()) {
			$fileRepository = t3lib_div::makeInstance('t3lib_file_Repository_FileRepository');
			$file = $fileRepository->findByUid($media->getFile()->getUid());
		}

		return $file ? $file->getName() : '';
	}

	/**
	 * Index the file into the database
	 *
	 * @param t3lib_file_File $file
	 */
	protected function index($file) {
		/** @var t3lib_file_Repository_FileRepository $fileRepository */
		$fileRepository = t3lib_div::makeInstance('t3lib_file_Repository_FileRepository');
		return $fileRepository->addToIndex($file);
	}

	/**
	 * Upload the file to the right directory
	 *
	 * @param t3lib_file_File $file
	 */
	protected function upload($uploadedFile) {
		/** @var $uploader t3lib_file_Service_UploaderService */
		$uploader = t3lib_div::makeInstance('t3lib_file_Service_UploaderService');

		if (isset($uploadedFile['name'])) {
			if ($uploadedFile['error']['file']) {
				// TODO handle error
			}

			$tempfileName = $uploadedFile['tmp_name'];
			$origFilename = $uploadedFile['name'];

			// @todo check if file must be overwritten
			$file = $uploader->addUploadedFile($tempfileName, $this->mount, '/', $origFilename, $overwrite = TRUE);
		}

		return $file;
	}

	/**
	 * Track uploads/* files
	 */
//	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, $tce) {
//	}
}

?>
