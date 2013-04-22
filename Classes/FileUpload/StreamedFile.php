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
 * Handle file uploads via XMLHttpRequest.
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class StreamedFile extends \TYPO3\CMS\Media\FileUpload\UploadedFileAbstract {

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
	 * Save the file to the specified path
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return boolean TRUE on success
	 */
	public function save() {

		if (is_null($this->uploadFolder)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Upload folder is not defined', 1361787579);
		}

		if (is_null($this->name)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('File name is not defined', 1361787580);
		}

		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()) {
			return FALSE;
		}

		$target = fopen($this->getFileWithAbsolutePath(), "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return TRUE;
	}

	/**
	 * Get the original file name.
	 *
	 * @return string
	 */
	public function getOriginalName() {
		return $_GET[$this->inputName];
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

?>