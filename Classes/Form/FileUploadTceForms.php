<?php
namespace Fab\Media\Form;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to render a file upload widget.
 * Notice the file is very similar to FileUpload.php but integrates itself into TCEforms.
 */
class FileUploadTceForms extends FileUpload {

	/**
	 * @var string
	 */
	protected $templateFile = 'Resources/Private/Backend/Standalone/FileUploadTceFormsTemplate.html';

	/**
	 * Fetch the JavaScript to be rendered and replace the markers with "live" variables.
	 *
	 * @return string
	 */
	protected function getJavaScript() {

		// Get the base prefix.
		$basePrefix = $this->getBasePrefix($this->getPrefix());
		$filePath = ExtensionManagementUtility::extPath('media') . 'Resources/Private/Backend/Standalone/FileUploadTceForms.js';

		return sprintf(file_get_contents($filePath),
			$basePrefix,
			$this->elementId,
			BackendUtility::getModuleUrl('user_MediaM1'),
			$this->getAllowedExtension(),
			GeneralUtility::getMaxUploadFileSize() * 1024,
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
		/** @var \Fab\Media\ViewHelpers\MetadataViewHelper $metadataViewHelper */
		$metadataViewHelper = GeneralUtility::makeInstance('Fab\Media\ViewHelpers\MetadataViewHelper');

		return sprintf('<div class="container-fileInfo" style="font-size: 7pt; color: #777;">%s</div>',
			$metadataViewHelper->render($this->file)
		);
	}
}
