<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011
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
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Media_Service_Thumbnail {

	/**
	 * The size of the Thumbnail
	 *
	 * @var string
	 */
	protected $size = '64x64';

	/**
	 * Force the thumbnail to be regenerated
	 *
	 * @var boolean
	 */
	protected $force = FALSE;
	
	/**
	 * Constructor
	 */
	public function __construct() {
	}
	
	/**
	 * Generates a Thumbnail File
	 *
	 * @param t3lib_file_Domain_Model_File $file
	 * @param t3lib_file_Domain_Model_Mount $mount
	 * @param array $parameters
	 * @return mixed
	 */
	public function createThumbnailFile(t3lib_file_Domain_Model_File $file, t3lib_file_Domain_Model_Mount $mount, array $parameters = array()) {

		$thumbnailPath = FALSE;
		if ($this->isThumbnailPossible($file)) {

				// Check file extension:
			$input = $file->getForLocalProcessing($writable = FALSE);

				// Computes Thumbnail absolute path
			$thumbnailName = preg_replace('/' . $file->getExtension() . '$/is', 'png', $file->getName());
			$thumbnailPath = PATH_site . 'typo3temp/' . $thumbnailName;

			if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['im']) {
					// If thumbnail does not exist, we generate it
				if (!file_exists($thumbnailPath) || $this->force) {
					$parameters = '-sample ' . $this->size . ' ' . $this->wrapFileName($input) . '[0] ' . $this->wrapFileName($thumbnailPath);
					$cmd = t3lib_div::imageMagickCommand('convert', $parameters);
					t3lib_utility_Command::exec($cmd);
					if (!file_exists($thumbnailPath)) {
						// @todo throw error
						//$this->errorGif('No thumb','generated!', basename($input));
					} else {
						t3lib_div::fixPermissions($thumbnailPath);
					}
				}
			}
		}
		else {
			// @todo throw error if debug context is detected
			//$this->errorGif('Not imagefile!', $ext, basename($input));
		}
		
		/** @var $uploader t3lib_file_Service_UploaderService */
		$uploader = t3lib_div::makeInstance('t3lib_file_Service_UploaderService');
		$thumbnailFile = $uploader->addUploadedFile($thumbnailPath, $mount, '/', $thumbnailName, TRUE);
				
		return $thumbnailFile;
	}

	/**
	 * Escapes a file name so it can safely be used on the command line.
	 *
	 * @param string $inputName filename to safeguard, must not be empty
	 *
	 * @return string $inputName escaped as needed
	 */
	protected function wrapFileName($inputName) {
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) {
			$currentLocale = setlocale(LC_CTYPE, 0);
			setlocale(LC_CTYPE, $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLocale']);
		}
		$escapedInputName = escapeshellarg($inputName);
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) {
			setlocale(LC_CTYPE, $currentLocale);
		}
		return $escapedInputName;
	}
	
	/**
	 * Checks if a image preview can be generated for a file
	 *
	 * @param	t3lib_file_Domain_Model_File $file
	 * @return	boolean
	 */
	protected function isThumbnailPossible(t3lib_file_Domain_Model_File $file) {

		// @todo get mimeType base on Service extraction
		$thumbnailPossible = FALSE;
		
		// font rendering is buggy so it's deactivated here   # if ($type === 'ttf' ||
		if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], $file->getExtension())) {
			$thumbnailPossible = TRUE;
		}
		return $thumbnailPossible;
	}

}
?>