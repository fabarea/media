<?php
namespace TYPO3\CMS\Media\Form\TceForms;

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
 * A class to render a file upload widget.
 * Notice the file is very similar to FileUpload.php but integrates itself into TCEforms.
 */
class FileUpload extends \TYPO3\CMS\Media\Form\FileUpload {

	/**
	 * Get the javascript from a file and replace the markers with live variables.
	 *
	 * @return string
	 */
	protected function getJavaScript() {

		// Get the base prefix
		$basePrefix = $this->getBasePrefix($this->getPrefix());
		$filePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('media') . 'Resources/Private/Backend/Standalone/FileUploadTceForms.js';

		return sprintf(file_get_contents($filePath),
			$basePrefix,
			$this->elementId,
			$this->getAllowedExtension(),
			\TYPO3\CMS\Core\Utility\GeneralUtility::getMaxUploadFileSize() * 1024,
			$this->getMaximumUploadLabel(),
			$this->getValue(),
			$this->getCallBack()
		);
	}

	/**
	 * Get allowed extension.
	 *
	 * @return string
	 */
	protected function getAllowedExtension() {
		return $this->fileObject->getExtension();
	}
}
