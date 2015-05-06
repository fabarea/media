<?php
namespace Fab\Media\Thumbnail;

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
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\Domain\Model\Selection;

/**
 * Thumbnail Generator for generating thumbnails in batch.
 */
class ThumbnailGenerator {

	/**
	 * @var int
	 */
	protected $numberOfTraversedFiles = 0;

	/**
	 * @var int
	 */
	protected $numberOfProcessedFiles = 0;

	/**
	 * @var int
	 */
	protected $numberOfMissingFiles = 0;

	/**
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * @var ResourceStorage
	 */
	protected $storage = NULL;

	/**
	 * @var Selection
	 */
	protected $selection = NULL;

	/**
	 * @var array
	 */
	protected $resultSet = array();

	/**
	 * @var array
	 */
	protected $newProcessedFileIdentifiers = array();

	/**
	 * Internal variable
	 *
	 * @var int
	 */
	protected $lastInsertedProcessedFile = 0;

	/**
	 * Generate
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return void
	 */
	public function generate($limit = 0, $offset = 0) {

		// Compute a possible limit and offset for the query.
		$limitAndOffset = '';
		if ($limit > 0 || $offset > 0) {
			$limitAndOffset = $limit . ' OFFSET ' . $offset;
		}

		// Retrieve file records.
		$clause = 'storage > 0';
		if ($this->storage) {
			$clause = 'storage = ' . $this->storage->getUid();
		}

		$query = $this->getDatabaseConnection()->SELECTquery('*', 'sys_file', $clause, '', '', $limitAndOffset);
		$resource = $this->getDatabaseConnection()->sql_query($query);

		while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($resource)) {

			$file = ResourceFactory::getInstance()->getFileObject($row['uid'], $row);

			if ($file->exists()) {

				$thumbnailUri = $this->getThumbnailService($file)
					->setOutputType(ThumbnailInterface::OUTPUT_URI)
					->setConfiguration($this->configuration)
					->create();

				$this->resultSet[$file->getUid()] = array(
					'fileUid' => $file->getUid(),
					'fileIdentifier' => $file->getIdentifier(),
					'thumbnailUri' => strpos($thumbnailUri, '_processed_') > 0 ? $thumbnailUri : '', // only returns the thumbnail uri if a processed file has been created.
				);

				if ($this->isNewProcessedFile()) {
					$this->incrementNumberOfProcessedFiles();
					$this->newProcessedFileIdentifiers[$file->getUid()] = $this->lastInsertedProcessedFile;
				}

				$this->incrementNumberOfTraversedFiles();
			} else {
				$this->incrementNumberOfMissingFiles();
			}
		}

	}

	/**
	 * @return int
	 */
	protected function isNewProcessedFile() {
		$isNewProcessedFile = FALSE;
		$lastInsertedId = $this->getDatabaseConnection()->sql_insert_id();
		if ($lastInsertedId > 0 && $lastInsertedId !== $this->lastInsertedProcessedFile) {
			$this->lastInsertedProcessedFile = $lastInsertedId;
			$isNewProcessedFile = TRUE;
		}
		return $isNewProcessedFile;
	}

	/**
	 * @return int
	 */
	public function getNumberOfTraversedFiles() {
		return $this->numberOfTraversedFiles;
	}

	/**
	 * @return int
	 */
	public function getNumberOfProcessedFiles() {
		return $this->numberOfProcessedFiles;
	}

	/**
	 * @return int
	 */
	public function getTotalNumberOfFiles() {
		$clause = 'storage > 0';
		if ($this->storage) {
			$clause = 'storage = ' . $this->storage->getUid();
		}
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('count(*) AS totalNumberOfFiles', 'sys_file', $clause);
		return (int)$record['totalNumberOfFiles'];
	}

	/**
	 * @return array
	 */
	public function getResultSet() {
		return $this->resultSet;
	}

	/**
	 * @return array
	 */
	public function getNewProcessedFileIdentifiers() {
		return $this->newProcessedFileIdentifiers;
	}

	/**
	 * @return int
	 */
	public function getNumberOfMissingFiles() {
		return $this->numberOfMissingFiles;
	}

	/**
	 * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
	 * @return $this
	 */
	public function setStorage($storage) {
		$this->storage = $storage;
		return $this;
	}

	/**
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Selection $selection
	 * @return $this
	 */
	public function setSelection($selection) {
		$this->selection = $selection;
		return $this;
	}

	/**
	 * @param array $configuration
	 * @return $this
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
		return $this;
	}

	/**
	 * @param File $file
	 * @return \Fab\Media\Thumbnail\ThumbnailService
	 */
	protected function getThumbnailService(File $file) {
		return GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailService', $file);
	}

	/**
	 * @return void
	 */
	protected function incrementNumberOfTraversedFiles() {
		$this->numberOfTraversedFiles++;
	}

	/**
	 * @return void
	 */
	protected function incrementNumberOfMissingFiles() {
		$this->numberOfMissingFiles++;
	}

	/**
	 * @return void
	 */
	protected function incrementNumberOfProcessedFiles() {
		$this->numberOfProcessedFiles++;
	}

	/**
	 * Returns a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

}
