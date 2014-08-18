<?php
namespace TYPO3\CMS\Media\Service;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service dealing with cache related to a File.
 */
class CacheService {

	/**
	 * Clear all possible cache related to a file.
	 * This method is useful when replacing a file for instance.
	 *
	 * @param File $file
	 * @return void
	 */
	public function clearCache(File $file) {

		$this->clearCachePages($file);
		$this->flushProcessedFiles($file);
	}

	/**
	 * Remove all processed files that belong to the given File object.
	 *
	 * @param File $file
	 * @return void
	 */
	protected function flushProcessedFiles(File $file) {

		/** @var $processedFile \TYPO3\CMS\Core\Resource\ProcessedFile */
		foreach ($this->getProcessedFileRepository()->findAllByOriginalFile($file) as $processedFile) {
			if ($processedFile->exists()) {
				$processedFile->delete(TRUE);
			}
			$this->getDatabaseConnection()->exec_DELETEquery('sys_file_processedfile', 'uid=' . (int)$processedFile->getUid());
		}
	}

	/**
	 * Return a processed file repository
	 *
	 * @return \TYPO3\CMS\Core\Resource\ProcessedFileRepository
	 */
	protected function getProcessedFileRepository() {
		return GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ProcessedFileRepository');
	}

	/**
	 * Returns the file references.
	 *
	 * @param File $file
	 * @return void
	 */
	protected function clearCachePages($file) {

		/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tce = GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		$tce->start(array(), array());

		$pages = array_merge(
			$this->findPagesWithFileReferences($file),
			$this->findPagesWithSoftReferences($file)
		);

		foreach (array_unique($pages) as $page) {
			$tce->clear_cache('pages', $page);
		}
	}

	/**
	 * Find all pages which contains file references to the given $file.
	 *
	 * @param File $file
	 * @return array
	 */
	protected function findPagesWithFileReferences($file) {

		// Get the file references of the file
		$rows = $this->getDatabaseConnection()->exec_SELECTquery(
			'DISTINCT pid',
			'sys_file_reference',
			'deleted = 0 AND pid > 0 AND uid_local = ' . $file->getUid()
		);

		// Compute result
		$pages = array();
		while ($affectedPage = $this->getDatabaseConnection()->sql_fetch_assoc($rows)) {
			$pages[] = $affectedPage['pid'];
		}

		return $pages;
	}

	/**
	 * Find all pages which have soft references to the given $file.
	 *
	 * @param File $file
	 * @return array
	 */
	protected function findPagesWithSoftReferences(File $file) {

		$subClauseParts = array(
			'deleted = 0',
			'(softref_key = "rtehtmlarea_images" OR softref_key = "typolink_tag")',
			'ref_table = "sys_file"',
			'tablename = "tt_content"',
			'ref_uid = ' . $file->getUid(),
		);

		$rows = $this->getDatabaseConnection()->exec_SELECTquery(
			'DISTINCT pid',
			'tt_content',
			sprintf('uid IN (SELECT recuid FROM sys_refindex WHERE %s) %s',
				implode(' AND ', $subClauseParts),
				$this->getWhereClauseForEnabledFields('tt_content')
			)
		);

		// Compute result
		$pages = array();
		while ($affectedPage = $this->getDatabaseConnection()->sql_fetch_assoc($rows)) {
			$pages[] = $affectedPage['pid'];
		}

		return $pages;
	}

	/**
	 * Get the WHERE clause for the enabled fields given a $tableName.
	 *
	 * @param string $tableName
	 * @return string
	 */
	protected function getWhereClauseForEnabledFields($tableName) {
		if ($this->isFrontendMode()) {
			// frontend context
			$whereClause = $this->getPageRepository()->deleteClause($tableName);
		} else {
			// backend context
			$whereClause = BackendUtility::deleteClause($tableName);
		}
		return $whereClause;
	}

	/**
	 * Returns whether the current mode is Frontend
	 *
	 * @return string
	 */
	protected function isFrontendMode() {
		return TYPO3_MODE == 'FE';
	}

	/**
	 * Returns an instance of the page repository.
	 *
	 * @return \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	protected function getPageRepository() {
		return $GLOBALS['TSFE']->sys_page;
	}

	/**
	 * Return whether the asset has no soft link references.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	protected function hasNotSoftLinkReferences($asset) {

		// Get the link references of the asset.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $asset->getUid()
		);

		return empty($softReferences);
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
