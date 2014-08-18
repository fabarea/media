<?php
namespace TYPO3\CMS\Media\Resource;

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

/**
 * File Reference Service.
 */
class FileReferenceService {

	/**
	 * Return all references found in sys_file_reference.
	 *
	 * @param File $file
	 * @return array
	 */
	public function findFileReferences(File $file) {

		// Get the file references of the file.
		return $this->getDatabaseConnection()->exec_SELECTgetRows(
			'*',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $file->getUid()
		);
	}

	/**
	 * Return soft image references.
	 *
	 * @param File $file
	 * @return array
	 */
	public function findSoftImageReferences(File $file) {

		// Get the file references of the file in the RTE.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $file->getUid()
		);

		return $softReferences;
	}

	/**
	 * Return link image references.
	 *
	 * @param File $file
	 * @return array
	 */
	public function findSoftLinkReferences(File $file) {

		// Get the link references of the file.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $file->getUid()
		);

		return $softReferences;
	}

	/**
	 * Count all references found in sys_file_reference.
	 *
	 * @param File $file
	 * @return int
	 */
	public function countFileReferences(File $file) {

		// Count the file references of the file.
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
			'count(*) AS count',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $file->getUid()
		);

		return (int)$record['count'];
	}

	/**
	 * Count soft image references.
	 *
	 * @param File $file
	 * @return int
	 */
	public function countSoftImageReferences(File $file) {

		// Count the file references of the file in the RTE.
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
			'count(*) AS count',
			'sys_refindex',
			'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $file->getUid()
		);

		return (int)$record['count'];
	}

	/**
	 * Count link image references.
	 *
	 * @param File $file
	 * @return int
	 */
	public function countSoftLinkReferences(File $file) {

		// Count the link references of the file.
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
			'count(*) AS count',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $file->getUid()
		);

		return (int)$record['count'];
	}

	/**
	 * Count total reference.
	 *
	 * @param File $file
	 * @return int
	 */
	public function countTotalReferences(File $file) {
		$numberOfReferences = $this->countFileReferences($file);
		$numberOfReferences +=  $this->countSoftImageReferences($file);
		$numberOfReferences +=  $this->countSoftLinkReferences($file);

		return $numberOfReferences;
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
