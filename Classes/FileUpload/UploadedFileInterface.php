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
 * A interface dealing with uploaded file.
 */
interface UploadedFileInterface {

	/**
	 * Save the file to the specified path.
	 *
	 * @return boolean TRUE on success
	 */
	public function save();

	/**
	 * Get the original filename.
	 *
	 * @return string filename
	 */
	public function getOriginalName();

	/**
	 * Get the file size.
	 *
	 * @return int
	 */
	public function getSize();

	/**
	 * Get the file name.
	 *
	 * @return int
	 */
	public function getName();

	/**
	 * Get the file type.
	 *
	 * @return int
	 */
	public function getType();

	/**
	 * Get the mime type of the file.
	 *
	 * @return int
	 */
	public function getMimeType();

	/**
	 * Get the file with its absolute path.
	 *
	 * @return string
	 */
	public function getFileWithAbsolutePath();

	/**
	 * Get the file's public URL.
	 *
	 * @return string
	 */
	public function getPublicUrl();

	/**
	 * Set the file input name from the DOM.
	 *
	 * @param string $inputName
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function setInputName($inputName);

	/**
	 * Set the upload folder
	 *
	 * @param string $uploadFolder
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function setUploadFolder($uploadFolder);

	/**
	 * Set the file name to be saved
	 *
	 * @param string $name
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function setName($name);
}
