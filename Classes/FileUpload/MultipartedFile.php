<?php
namespace TYPO3\CMS\Media\FileUpload;

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
 * Handle file uploads via regular form post (uses the $_FILES array)
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MultipartedFile implements \TYPO3\CMS\Media\FileUpload\UploadedFileInterface {

	/**
	 * @var string
	 */
	protected $inputName;

	/**
	 * @return \TYPO3\CMS\Media\FileUpload\MultipartedFile
	 */
	public function __construct() {
	}

	/**
	 * Save the file to the specified path
	 *
	 * @return boolean TRUE on success
	 */
	public function save() {
		return move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $this->getFileWithAbsolutePath());
	}

	/**
	 * Get the original filename
	 *
	 * @return string filename
	 */
	public function getOriginalName() {
		return $_FILES[$this->inputName]['name'];
	}

	/**
	 * Get the file size
	 *
	 * @return integer file-size in byte
	 */
	public function getSize() {
		return $_FILES[$this->inputName]['size'];
	}

	/**
	 * Get the file type.
	 *
	 * @return int
	 */
	public function getType() {
		$_FILES[$this->inputName]['type'];
	}

	/**
	 * Set the file input name from the DOM.
	 *
	 * @param string $inputName
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function setInputName($inputName) {
		// TODO: Implement setInputName() method.
	}

	/**
	 * Set the upload folder
	 *
	 * @param string $uploadFolder
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function setUploadFolder($uploadFolder) {
		// TODO: Implement setUploadFolder() method.
	}

	/**
	 * Get the file with its absolute path.
	 *
	 * @return string
	 */
	public function getFileWithAbsolutePath() {
		// TODO: Implement getFileWithAbsolutePath() method.
	}

	/**
	 * Get the mime type of the file.
	 *
	 * @return int
	 */
	public function getMimeType() {
		// TODO: Implement getMimeType() method.
	}

	/**
	 * Set the file name to be saved
	 *
	 * @param string $name
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function setName($name) {
		// TODO: Implement setName() method.
	}

	/**
	 * Get the file name.
	 *
	 * @return int
	 */
	public function getName() {
		// TODO: Implement getName() method.
	}
}
?>