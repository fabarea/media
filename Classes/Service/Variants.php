<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Steffen Ritter <steffen.ritter@typo3.org>
 *
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
 *
 *
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Media_Service_Variants {

	/**
	 * @var t3lib_file_Repository_FileRepository
	 */
	protected $fileRepository = NULL;

	public function __construct() {
		$this->fileRepository = t3lib_div::makeInstance('t3lib_file_Repository_FileRepository');
	}
	
	/**
	 * Get meta information from a file using a metaExtract service
	 *
	 * @param t3lib_file_File $file
	 * @param int|null $restrictToVariantType
	 * @return t3lib_file_File[]
	 */
	public function getVariantsOfFile(t3lib_file_File $file, $restrictToVariantType = NULL) {
		$file = $this->findOriginal($file);
		$variants = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'variant',
			'sys_file_variants',
			'original=' . $file->getUid() .
			($restrictToVariantType == NULL ?: ' AND role = ' . intval($restrictToVariantType))
		);
		$variantsArray = array();
		foreach ($variants AS $rawVariant) {
			$object = $this->fileRepository->findByUid($rawVariant['variant']);
			if ($object instanceof t3lib_file_File) {
				$variantsArray[] = $object;
			}
		}

		return $variantsArray;
	}

	/**
	 * Retrieves thumbnail for file placed in record
	 *
	 * @param t3lib_file_File $file
	 * @return t3lib_file_File
	 */
	public function getThumbnailForFile(t3lib_file_File $file) {
		return current($this->getVariantsOfFile($file, 4));
	}

	public function getAlternateFiles(t3lib_file_File $file, $restrictToFileExtensions = NULL) {
		$files = $this->getVariantsOfFile($file, 1);

		if ($restrictToFileExtensions !== NULL) {
			$filteredFiles = array();
			foreach ($files AS $file) {
				if (t3lib_div::inList($restrictToFileExtensions, $file->getExtension())) {
					$filteredFiles[] = $file;
				}
			}
			$files = $filteredFiles;
		}
		return $files;
	}

	/**
	 * checks wether the given file is used as variant and returns the original file due to metadata
	 *
	 * @param t3lib_file_File $file
	 * @return t3lib_file_File
	 */
	public function findOriginal(t3lib_file_File $file) {
		$variants = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'original',
			'sys_file_variants',
			'variant = ' . $file->getUid()
		);
		if (count($variants)) {
			$orig = $this->fileRepository->findByUid($variants[0]['original']);
			if ($orig !== NULL)  {
				$file = $orig;
			}
		}
		return $file;
	}

}
?>