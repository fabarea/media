<?php
namespace TYPO3\CMS\Media\Override\Core\Resource;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Andreas Wolf <andreas.wolf@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class ResourceStorage extends \TYPO3\CMS\Core\Resource\ResourceStorage {

	/**
	 * Check if a file has the permission to be uploaded to a Folder/Storage,
	 * if not throw an exception
	 *
	 * @param string $localFilePath the temporary file name from $_FILES['file1']['tmp_name']
	 * @param \TYPO3\CMS\Core\Resource\Folder $targetFolder
	 * @param string $targetFileName the destination file name $_FILES['file1']['name']
	 * @param int $uploadedFileSize
	 * @return void
	 */
	protected function checkFileUploadPermissions($localFilePath, $targetFolder, $targetFileName, $uploadedFileSize) {
		// Makes sure the user is allowed to upload
		if (!$this->checkUserActionPermission('upload', 'File')) {
			throw new \TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException('You are not allowed to upload files to this storage "' . $this->getUid() . '"', 1322112430);
		}

		// Makes sure this is an uploaded file
		// commented from the original implementation for the sake of XHR file upload.
		#if (!is_uploaded_file($localFilePath)) {
		#	throw new \TYPO3\CMS\Core\Resource\Exception\UploadException('The upload has failed, no uploaded file found!', 1322110455);
		#}
		// Max upload size (kb) for files.
		$maxUploadFileSize = \TYPO3\CMS\Core\Utility\GeneralUtility::getMaxUploadFileSize() * 1024;
		if ($uploadedFileSize >= $maxUploadFileSize) {
			throw new \TYPO3\CMS\Core\Resource\Exception\UploadSizeException('The uploaded file exceeds the size-limit of ' . $maxUploadFileSize . ' bytes', 1322110041);
		}
		// Check if targetFolder is writable
		if (!$this->checkFolderActionPermission('write', $targetFolder)) {
			throw new \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException('You are not allowed to write to the target folder "' . $targetFolder->getIdentifier() . '"', 1322120356);
		}
		// Check for a valid file extension
		if (!$this->checkFileExtensionPermission($targetFileName)) {
			throw new \TYPO3\CMS\Core\Resource\Exception\IllegalFileExtensionException('Extension of file name is not allowed in "' . $targetFileName . '"!', 1322120271);
		}
	}

}

?>