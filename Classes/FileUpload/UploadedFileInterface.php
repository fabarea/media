<?php
namespace TYPO3\CMS\Media\FileUpload;

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
