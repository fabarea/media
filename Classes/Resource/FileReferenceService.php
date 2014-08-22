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
	 * @param File|int $file
	 * @return array
	 */
	public function findFileReferences($file) {

		$fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

		// Get the file references of the file.
		return $this->getDatabaseConnection()->exec_SELECTgetRows(
			'*',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $fileIdentifier
		);
	}

	/**
	 * Return soft image references.
	 *
	 * @param File|int $file
	 * @return array
	 */
	public function findSoftImageReferences($file) {

		$fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

		// Get the file references of the file in the RTE.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $fileIdentifier
		);

		return $softReferences;
	}

	/**
	 * Return link image references.
	 *
	 * @param File|int $file
	 * @return array
	 */
	public function findSoftLinkReferences($file) {

		$fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

		// Get the link references of the file.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $fileIdentifier
		);

		return $softReferences;
	}

	/**
	 * Count all references found in sys_file_reference.
	 *
	 * @param File|int $file
	 * @return int
	 */
	public function countFileReferences($file) {

		$fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

		// Count the file references of the file.
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
			'count(*) AS count',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $fileIdentifier
		);

		return (int)$record['count'];
	}

	/**
	 * Count soft image references.
	 *
	 * @param File|int $file
	 * @return int
	 */
	public function countSoftImageReferences($file) {

		$fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

		// Count the file references of the file in the RTE.
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
			'count(*) AS count',
			'sys_refindex',
			'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $fileIdentifier
		);

		return (int)$record['count'];
	}

	/**
	 * Count link image references.
	 *
	 * @param File|int $file
	 * @return int
	 */
	public function countSoftLinkReferences($file) {

		$fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

		// Count the link references of the file.
		$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
			'count(*) AS count',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $fileIdentifier
		);

		return (int)$record['count'];
	}

	/**
	 * Count total reference.
	 *
	 * @param File|int $file
	 * @return int
	 */
	public function countTotalReferences($file) {
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
