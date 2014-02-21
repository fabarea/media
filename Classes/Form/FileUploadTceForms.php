<?php
namespace TYPO3\CMS\Media\Form;

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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to render a file upload widget.
 * Notice the file is very similar to FileUpload.php but integrates itself into TCEforms.
 */
class FileUploadTceForms extends FileUpload {

	/**
	 * Fetch the JavaScript to be rendered and replace the markers with "live" variables.
	 *
	 * @return string
	 */
	protected function getJavaScript() {

		// Get the base prefix
		$basePrefix = $this->getBasePrefix($this->getPrefix());
		$filePath = ExtensionManagementUtility::extPath('media') . 'Resources/Private/Backend/Standalone/FileUploadTceForms.js';

		return sprintf(file_get_contents($filePath),
			$basePrefix,
			$this->elementId,
			$this->getAllowedExtension(),
			GeneralUtility::getMaxUploadFileSize() * 1024,
			$this->getMaximumUploadLabel(),
			$this->getValue()
		);
	}

	/**
	 * Get allowed extension.
	 *
	 * @return string
	 */
	protected function getAllowedExtension() {
		return $this->file->getExtension();
	}

	/**
	 * Returns additional file info.
	 *
	 * @return string
	 */
	protected function getFileInfo() {
		/** @var \TYPO3\CMS\Media\ViewHelpers\MetadataViewHelper $metadataViewHelper */
		$metadataViewHelper = GeneralUtility::makeInstance('TYPO3\CMS\Media\ViewHelpers\MetadataViewHelper');

		return sprintf('<div class="container-fileInfo" style="font-size: 7pt; color: #777;">%s</div>',
			$metadataViewHelper->render($this->file)
		);
	}
}
