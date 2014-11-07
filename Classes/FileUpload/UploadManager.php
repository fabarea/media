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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Exception\FailedFileUploadException;
use TYPO3\CMS\Media\Utility\PermissionUtility;

/**
 * Class that encapsulates the file-upload internals
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 */
class UploadManager {

	const UPLOAD_FOLDER = 'typo3temp/pics';

	/**
	 * @var int|NULL|string
	 */
	protected $sizeLimit;

	/**
	 * @var string
	 */
	protected $uploadFolder;

	/**
	 * @var FormUtility
	 */
	protected $formUtility;

	/**
	 * @var \TYPO3\CMS\Core\Resource\ResourceStorage
	 */
	protected $storage;

	/**
	 * Name of the file input in the DOM.
	 *
	 * @var string
	 */
	protected $inputName = 'qqfile';

	/**
	 * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
	 * @return UploadManager
	 */
	function __construct($storage = NULL) {

		$this->initializeUploadFolder();

		// max file size in bytes
		$this->sizeLimit = GeneralUtility::getMaxUploadFileSize() * 1024;
		$this->checkServerSettings();

		$this->formUtility = FormUtility::getInstance();
		$this->storage = $storage;
	}

	/**
	 * Handle the uploaded file.
	 *
	 * @return UploadedFileInterface
	 */
	public function handleUpload() {

		/** @var $uploadedFile UploadedFileInterface */
		$uploadedFile = FALSE;
		if ($this->formUtility->isMultiparted()) {

			// Default case
			$uploadedFile = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\MultipartedFile');
		} elseif ($this->formUtility->isOctetStreamed()) {

			// Fine Upload plugin would use it if forceEncoded = false and paramsInBody = false
			$uploadedFile = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\StreamedFile');
		} elseif ($this->formUtility->isUrlEncoded()) {

			// Used for image resizing in BE
			$uploadedFile = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\Base64File');
		}

		if (!$uploadedFile) {
			$this->throwException('Could not instantiate an upload object... No file was uploaded?');
		}

		$fileName = $this->getFileName($uploadedFile);

		$this->checkFileSize($uploadedFile->getSize());
		$this->checkFileAllowed($fileName);

		$saved = $uploadedFile->setInputName($this->inputName)
			->setUploadFolder($this->uploadFolder)
			->setName($fileName)
			->save();

		if (!$saved) {
			$this->throwException('Could not save uploaded file. The upload was cancelled, or server error encountered');
		}

		// Optimize file if the uploaded file is an image.
		if ($uploadedFile->getType() == \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE) {
			$uploadedFile = ImageOptimizer::getInstance($this->storage)->optimize($uploadedFile);
		}
		return $uploadedFile;
	}

	/**
	 * Internal function that checks if server's may sizes match the
	 * object's maximum size for uploads.
	 *
	 * @return void
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
	 * Return a file name given an uploaded file
	 *
	 * @param UploadedFileInterface $uploadedFile
	 * @return string
	 */
	public function getFileName(UploadedFileInterface $uploadedFile) {
		$pathInfo = pathinfo($uploadedFile->getOriginalName());
		$fileName = $this->sanitizeFileName($pathInfo['filename']);
		$fileNameWithExtension = $fileName;
		if (!empty($pathInfo['extension'])) {
			$fileNameWithExtension = sprintf('%s.%s', $fileName, $pathInfo['extension']);
		}
		return $fileNameWithExtension;
	}

	/**
	 * Check whether the file size does not exceed the allowed limit
	 *
	 * @param int $size
	 */
	public function checkFileSize($size) {
		if ($size == 0) {
			$this->throwException('File is empty');
		}

		if ($size > $this->sizeLimit) {
			$this->throwException('File is too large');
		}
	}

	/**
	 * Check whether the file is allowed
	 *
	 * @param string $fileName
	 */
	public function checkFileAllowed($fileName) {
		$isAllowed = $this->checkFileExtensionPermission($fileName);
		if (!$isAllowed) {
			$these = PermissionUtility::getInstance()->getAllowedExtensionList();
			$this->throwException('File has an invalid extension, it should be one of ' . $these . '.');
		}
	}

	/**
	 * If the fileName is given, check it against the
	 * TYPO3_CONF_VARS[BE][fileDenyPattern] + and if the file extension is allowed
	 *
	 * @see \TYPO3\CMS\Core\Resource\ResourceStorage->checkFileExtensionPermission($fileName);
	 * @param string $fileName Full filename
	 * @return boolean TRUE if extension/filename is allowed
	 */
	public function checkFileExtensionPermission($fileName) {
		$isAllowed = GeneralUtility::verifyFilenameAgainstDenyPattern($fileName);
		if ($isAllowed) {
			$fileInfo = GeneralUtility::split_fileref($fileName);
			// Set up the permissions for the file extension
			$fileExtensionPermissions = $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']['webspace'];
			$fileExtensionPermissions['allow'] = GeneralUtility::uniqueList(strtolower($fileExtensionPermissions['allow']));
			$fileExtensionPermissions['deny'] = GeneralUtility::uniqueList(strtolower($fileExtensionPermissions['deny']));
			$fileExtension = strtolower($fileInfo['fileext']);
			if ($fileExtension !== '') {
				// If the extension is found amongst the allowed types, we return TRUE immediately
				if ($fileExtensionPermissions['allow'] === '*' || GeneralUtility::inList($fileExtensionPermissions['allow'], $fileExtension)) {
					return TRUE;
				}
				// If the extension is found amongst the denied types, we return FALSE immediately
				if ($fileExtensionPermissions['deny'] === '*' || GeneralUtility::inList($fileExtensionPermissions['deny'], $fileExtension)) {
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
	 * Sanitize the file name for the web.
	 * It has been noticed issues when letting done this work by FAL. Give it a little hand.
	 *
	 * @see https://github.com/alixaxel/phunction/blob/master/phunction/Text.php#L252
	 * @param string $fileName
	 * @param string $slug
	 * @param string $extra
	 * @return string
	 */
	public function sanitizeFileName($fileName, $slug = '-', $extra = NULL) {
		return trim(preg_replace('~[^0-9a-z_' . preg_quote($extra, '~') . ']+~i', $slug, $this->unAccent($fileName)), $slug);
	}

	/**
	 * Remove accent from a string
	 *
	 * @see https://github.com/alixaxel/phunction/blob/master/phunction/Text.php#L297
	 * @param $string
	 * @return string
	 */
	protected function unAccent($string) {
		if (extension_loaded('intl') === true) {
			$string = \Normalizer::normalize($string, \Normalizer::FORM_KD);
		}

		if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
			$string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|caron|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
		}

		return $string;
	}

	/**
	 * @throws FailedFileUploadException
	 * @param string $message
	 */
	protected function throwException($message) {
		throw new FailedFileUploadException($message, 1357510420);
	}

	/**
	 * Initialize Upload Folder.
	 *
	 * @return void
	 */
	protected function initializeUploadFolder() {
		$this->uploadFolder = PATH_site . self::UPLOAD_FOLDER;

		// Initialize the upload folder for file transfer and create it if not yet existing
		if (!file_exists($this->uploadFolder)) {
			GeneralUtility::mkdir($this->uploadFolder);
		}

		// Check whether the upload folder is writable
		if (!is_writable($this->uploadFolder)) {
			$this->throwException("Server error. Upload directory isn't writable.");
		}
	}

	/**
	 * @return int|NULL|string
	 */
	public function getSizeLimit() {
		return $this->sizeLimit;
	}

	/**
	 * @param int|NULL|string $sizeLimit
	 * @return $this
	 */
	public function setSizeLimit($sizeLimit) {
		$this->sizeLimit = $sizeLimit;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUploadFolder() {
		return $this->uploadFolder;
	}

	/**
	 * @param string $uploadFolder
	 * @return $this
	 */
	public function setUploadFolder($uploadFolder) {
		$this->uploadFolder = $uploadFolder;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getInputName() {
		return $this->inputName;
	}

	/**
	 * @param string $inputName
	 * @return $this
	 */
	public function setInputName($inputName) {
		$this->inputName = $inputName;
		return $this;
	}

	/**
	 * @return \TYPO3\CMS\Core\Resource\ResourceStorage
	 */
	public function getStorage() {
		return $this->storage;
	}

	/**
	 * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
	 * @return $this
	 */
	public function setStorage($storage) {
		$this->storage = $storage;
		return $this;
	}

}
