<?php
namespace TYPO3\CMS\Media\FileUpload;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 */
class MultipartedFile extends UploadedFileAbstract {

	/**
	 * @var string
	 */
	protected $inputName = 'qqfile';

	/**
	 * @return \TYPO3\CMS\Media\FileUpload\MultipartedFile
	 */
//	public function __construct() {
//	}

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
	 * Get the mime type of the file.
	 *
	 * @return int
	 */
	public function getMimeType() {
		return $_FILES[$this->inputName]['type'];
	}
}
?>