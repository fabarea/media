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
 * Class that encapsulates the file-upload internals
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class UploadManager {

	/**
	 * @var array
	 */
	protected $allowedExtensions;

	/**
	 * @var int|NULL|string
	 */
	protected $sizeLimit;

	/**
	 * @var \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	protected $file;

	/**
	 * @var string
	 */
	protected $uploadName;

	/**
	 * @param array $allowedExtensions; defaults to an empty array
	 * @param string $inputName; defaults to the javascript default: 'qqfile'
	 * @return \TYPO3\CMS\Media\FileUpload\UploadManager
	 */
	function __construct($inputName = 'qqfile', array $allowedExtensions = array()) {

		$this->allowedExtensions = $allowedExtensions;

		// max file size in bytes
		$this->sizeLimit = \TYPO3\CMS\Core\Utility\GeneralUtility::getMaxUploadFileSize() * 1024;

		$this->checkServerSettings();

		if (!isset($_SERVER['CONTENT_TYPE'])) {
			$this->file = false;
		} else if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') === 0) {
			$this->file = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\MultipartedFile', $inputName);
		} else {
			$this->file = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\StreamedFile', $inputName);
		}
	}

	/**
	 * Get the name of the uploaded file
	 *
	 * @return string
	 */
	public function getUploadName() {
		if (isset($this->uploadName))
			return $this->uploadName;
	}

	/**
	 * @param string $uploadName
	 */
	public function setUploadName($uploadName) {
		$this->uploadName = $uploadName;
	}

	/**
	 * Get the original filename
	 *
	 * @return string filename
	 */
	public function getName() {
		if ($this->file)
			return $this->file->getName();
	}

	/**
	 * Internal function that checks if server's may sizes match the
	 * object's maximum size for uploads.
	 */
	protected function checkServerSettings() {
		$postSize = $this->toBytes(ini_get('post_max_size'));

		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			$this->throwException('increase post_max_size and upload_max_filesize to ' . $size);
		}
	}

	/**
	 * Convert a given size with units to bytes.
	 *
	 * @param string $str
	 * @return int|string
	 */
	protected function toBytes($str) {
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}

	/**
	 * Handle the uploaded file.
	 *
	 * @param string $uploadDirectory
	 * @param mixed $overWrite
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	function handleUpload($uploadDirectory, $overWrite = FALSE) {
		if (!is_writable($uploadDirectory)) {
			$this->throwException("Server error. Upload directory isn't writable.");
		}

		if (!$this->file) {
			$this->throwException('No files were uploaded.');
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			$this->throwException('File is empty');
		}

		if ($size > $this->sizeLimit) {
			$this->throwException('File is too large');
		}

		$pathInfo = pathinfo($this->file->getName());
		$filename = $pathInfo['filename'];
		//$filename = md5(uniqid());
		$ext = empty($pathInfo['extension']) ? '' : $pathInfo['extension'];

		$isAllowed = $this->checkFileExtensionPermission($this->file->getName());
		if (!$isAllowed) {
			$these = implode(', ', $this->allowedExtensions);
			$this->throwException('File has an invalid extension, it should be one of ' . $these . '.');
		}

		$ext = ($ext == '') ? $ext : '.' . $ext;

		if (! $overWrite) {
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename . $ext)) {
				$filename .= rand(10, 99);
			}
		}

		$this->uploadName = $filename . $ext;

		$saved = $this->file->save($uploadDirectory . DIRECTORY_SEPARATOR . $filename . $ext);
		if (! $saved) {
			$this->throwException('Could not save uploaded file. The upload was cancelled, or server error encountered');
		}
		return $this->file;
	}

	/**
	 * If the fileName is given, check it against the
	 * TYPO3_CONF_VARS[BE][fileDenyPattern] + and if the file extension is allowed
	 *
	 * @see \TYPO3\CMS\Core\Resource\ResourceStorage->checkFileExtensionPermission($fileName);
	 *
	 * @param string $fileName Full filename
	 * @return boolean TRUE if extension/filename is allowed
	 */
	public function checkFileExtensionPermission($fileName) {
		$isAllowed = \TYPO3\CMS\Core\Utility\GeneralUtility::verifyFilenameAgainstDenyPattern($fileName);
		if ($isAllowed) {
			$fileInfo = \TYPO3\CMS\Core\Utility\GeneralUtility::split_fileref($fileName);
			// Set up the permissions for the file extension
			$fileExtensionPermissions = $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']['webspace'];
			$fileExtensionPermissions['allow'] = \TYPO3\CMS\Core\Utility\GeneralUtility::uniqueList(strtolower($fileExtensionPermissions['allow']));
			$fileExtensionPermissions['deny'] = \TYPO3\CMS\Core\Utility\GeneralUtility::uniqueList(strtolower($fileExtensionPermissions['deny']));
			$fileExtension = strtolower($fileInfo['fileext']);
			if ($fileExtension !== '') {
				// If the extension is found amongst the allowed types, we return TRUE immediately
				if ($fileExtensionPermissions['allow'] === '*' || \TYPO3\CMS\Core\Utility\GeneralUtility::inList($fileExtensionPermissions['allow'], $fileExtension)) {
					return TRUE;
				}
				// If the extension is found amongst the denied types, we return FALSE immediately
				if ($fileExtensionPermissions['deny'] === '*' || \TYPO3\CMS\Core\Utility\GeneralUtility::inList($fileExtensionPermissions['deny'], $fileExtension)) {
					return FALSE;
				}
				// If no match we return TRUE
				return TRUE;
			} else {
				if ($fileExtensionPermissions['allow'] === '*') {
					return TRUE;
				}
				if ($fileExtensionPermissions['deny'] === '*') {
					return FALSE;
				}
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @throws \TYPO3\CMS\Media\Exception\FailedFileUploadException
	 * @param string $message
	 */
	public function throwException($message) {
		throw new \TYPO3\CMS\Media\Exception\FailedFileUploadException($message, 1357510420);
	}
}
?>