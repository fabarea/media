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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handle a posted file encoded in base 64.
 */
class Base64File extends \TYPO3\CMS\Media\FileUpload\UploadedFileAbstract {

	/**
	 * @var string
	 */
	protected $inputName = 'qqfile';

	/**
	 * @var string
	 */
	protected $uploadFolder;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $image;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @return \TYPO3\CMS\Media\FileUpload\Base64File
	 */
	public function __construct() {

		// Processes the encoded image data and returns the decoded image
		$encodedImage = GeneralUtility::_POST($this->inputName);
		if (preg_match('/^data:image\/(jpg|jpeg|png)/i', $encodedImage, $matches)) {
			$this->extension = $matches[1];
		} else {
			return FALSE;
		}

		// Remove the mime-type header
		$data = reset(array_reverse(explode('base64,', $encodedImage)));

		// Use strict mode to prevent characters from outside the base64 range
		$this->image = base64_decode($data, true);

		if (!$this->image) {
			return FALSE;
		}

		$this->setName(uniqid() . '.' . $this->extension);
	}

	/**
	 * Save the file to the specified path
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return boolean TRUE on success
	 */
	public function save() {

		if (is_null($this->uploadFolder)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Upload folder is not defined', 1362587741);
		}

		if (is_null($this->name)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('File name is not defined', 1362587742);
		}

		return file_put_contents($this->getFileWithAbsolutePath(), $this->image) > 0;
	}

	/**
	 * Get the original file name.
	 *
	 * @return string
	 */
	public function getOriginalName() {
		return $this->getName();
	}

	/**
	 * Get the file size
	 *
	 * @throws \Exception
	 * @return integer file-size in byte
	 */
	public function getSize() {
		if (isset($GLOBALS['_SERVER']['CONTENT_LENGTH'])) {
			return (int) $GLOBALS['_SERVER']['CONTENT_LENGTH'];
		} else {
			throw new \Exception('Getting content length is not supported.');
		}
	}

	/**
	 * Get MIME type of file.
	 *
	 * @return string|boolean MIME type. eg, text/html, FALSE on error
	 */
	public function getMimeType() {
		$this->checkFileExistence();
		if (function_exists('finfo_file')) {
			$fileInfo = new \finfo();
			return $fileInfo->file($this->getFileWithAbsolutePath(), FILEINFO_MIME_TYPE);
		} elseif (function_exists('mime_content_type')) {
			return mime_content_type($this->getFileWithAbsolutePath());
		}
		return FALSE;
	}
}
