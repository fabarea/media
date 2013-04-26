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
	 * @var int|NULL|string
	 */
	protected $sizeLimit;

	/**
	 * @var string
	 */
	protected $uploadFolder;

	/**
	 * @var \TYPO3\CMS\Media\FileUpload\FormUtility
	 */
	protected $formUtility;

	/**
	 * Name of the file input in the DOM.
	 *
	 * @var string
	 */
	protected $inputName = 'qqfile';

	/**
	 * @return \TYPO3\CMS\Media\FileUpload\UploadManager
	 */
	function __construct() {

		// Initialize the upload folder for file transfer and create it if not yet existing
		$this->uploadFolder = PATH_site . 'typo3temp/pics';
		if (!file_exists($this->uploadFolder)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir($this->uploadFolder);
		}

		// Check whether the upload folder is writable
		if (!is_writable($this->uploadFolder)) {
			$this->throwException("Server error. Upload directory isn't writable.");
		}

		// max file size in bytes
		$this->sizeLimit = \TYPO3\CMS\Core\Utility\GeneralUtility::getMaxUploadFileSize() * 1024;
		$this->checkServerSettings();

		$this->formUtility = \TYPO3\CMS\Media\FileUpload\FormUtility::getInstance();
	}


	/**
	 * Handle the uploaded file.
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function handleUpload() {

		/** @var $uploadedFile \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
		$uploadedFile = FALSE;
		if ($this->formUtility->isMultiparted()) {

			// Default case
			$uploadedFile = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\MultipartedFile');
		} elseif ($this->formUtility->isOctetStreamed()) {

			// Fine Upload plugin would use it if forceEncoded = false and paramsInBody = false
			$uploadedFile = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\StreamedFile');
		} elseif ($this->formUtility->isUrlEncoded()) {

			// Used for image resizing in BE
			$uploadedFile = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\Base64File');
		}

		if (!$uploadedFile) {
			$this->throwException('Couldn\'t instantiate an upload object... No file was uploaded?');
		}

		$fileName = $this->getFileName($uploadedFile);

		$this->checkFileSize($uploadedFile->getSize());
		$this->checkFileAllowed($fileName);

		$saved = $uploadedFile->setInputName($this->inputName)
			->setUploadFolder($this->uploadFolder)
			->setName($fileName)
			->save();

		if (! $saved) {
			$this->throwException('Could not save uploaded file. The upload was cancelled, or server error encountered');
		}

		// Optimize file if the uploaded file is an image.
		if ($uploadedFile->getType() == \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE) {
			$uploadedFile = \TYPO3\CMS\Media\FileUpload\ImageOptimizer::getInstance()->optimize($uploadedFile);
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
	 * @param \TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile
	 * @return string
	 */
	public function getFileName(\TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile){
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
	public function checkFileSize($size){
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
		$allowedExtensions = \TYPO3\CMS\Media\Utility\Permission::getInstance()->getAllowedExtensions();

		$isAllowed = $this->checkFileExtensionPermission($fileName);
		if (!$isAllowed) {
			$these = implode(', ', $allowedExtensions);
			$this->throwException('File has an invalid extension, it should be one of ' . $these . '.');
		}
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
	 * Sanitize the file name for the web.
	 * It has been noticed issues when letting done this work by FAL. Give it a little hand.
	 *
	 * @see https://github.com/alixaxel/phunction/blob/master/phunction/Text.php#L252
	 *
	 * @param string $fileName
	 * @param string $slug
	 * @param string $extra
	 * @return string
	 */
	public function sanitizeFileName($fileName, $slug = '-', $extra = NULL){
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
			$string = Normalizer::normalize($string, Normalizer::FORM_KD);
		}

		if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
			$string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|caron|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
		}

		return $string;
	}

	/**
	 * @throws \TYPO3\CMS\Media\Exception\FailedFileUploadException
	 * @param string $message
	 */
	public function throwException($message) {
		throw new \TYPO3\CMS\Media\Exception\FailedFileUploadException($message, 1357510420);
	}

	/**
	 * @return int|NULL|string
	 */
	public function getSizeLimit() {
		return $this->sizeLimit;
	}

	/**
	 * @param int|NULL|string $sizeLimit
	 */
	public function setSizeLimit($sizeLimit) {
		$this->sizeLimit = $sizeLimit;
	}

	/**
	 * @return string
	 */
	public function getUploadFolder() {
		return $this->uploadFolder;
	}

	/**
	 * @param string $uploadFolder
	 */
	public function setUploadFolder($uploadFolder) {
		$this->uploadFolder = $uploadFolder;
	}

	/**
	 * @return string
	 */
	public function getInputName() {
		return $this->inputName;
	}

	/**
	 * @param string $inputName
	 */
	public function setInputName($inputName) {
		$this->inputName = $inputName;
	}

}
?>