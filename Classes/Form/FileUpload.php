<?php
namespace TYPO3\CMS\Media\Form;

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
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Media\Service\ThumbnailInterface;
use TYPO3\CMS\Media\Utility\PermissionUtility;

/**
 * A class to render a file upload widget.
 */
class FileUpload extends \TYPO3\CMS\Media\Form\AbstractFormField  {

	/**
	 * @var string
	 */
	protected $elementId;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File
	 */
	protected $file;

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 */
	protected $pageRenderer;

	/**
	 * @return \TYPO3\CMS\Media\Form\FileUpload
	 */
	public function __construct() {
		// Example:
		// <input multiple = "false" name = "tx_media_user_mediam1[media][name]" type ="file" >
		// <input name = "file[upload][1][target]" value = "1:/user_upload/images/persons/" type = "hidden" >

		// language labels for JavaScript files
		$this->pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();
		$this->pageRenderer->addInlineLanguageLabelFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('media') . 'Resources/Private/Language/locallang.xlf', 'media_file_upload');

		$this->elementId = 'jquery-wrapped-fine-uploader-' . uniqid();

		$this->template = <<<EOF
<div class="control-group control-group-upload">
    <div class="container-thumbnail">%s</div>
    %s
    <div id="%s"></div>
	<script type="text/javascript">
	    %s
	</script>
</div>

EOF;
	}

	/**
	 * Render a file upload field.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return string
	 */
	public function render() {

		// Instantiate the file object for the whole class if possible.
		if ($this->getValue()) {
			$this->file = ResourceFactory::getInstance()->getFileObject($this->getValue());
		}

		$result = sprintf($this->template,
			$this->getThumbnail(),
			$this->getFileInfo(),
			$this->elementId,
			$this->getJavaScript()
		);
		return $result;
	}

	/**
	 * Get the javascript from a file and replace the markers with live variables.
	 *
	 * @return string
	 */
	protected function getThumbnail() {
		$thumbnail = '';
		if ($this->file) {

			/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
			$thumbnailService = GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
			$thumbnail = $thumbnailService
				->setFile($this->file)
				->setOutputType(ThumbnailInterface::OUTPUT_IMAGE_WRAPPED)
				->setAppendTimeStamp(TRUE)
				->create();
		}
		return $thumbnail;
	}
	/**
	 * Get the javascript from a file and replace the markers with live variables.
	 *
	 * @return string
	 */
	protected function getJavaScript() {

		// Get the base prefix
		$basePrefix = $this->getBasePrefix($this->getPrefix());

		$filePath = ExtensionManagementUtility::extPath('media') . 'Resources/Private/Backend/Standalone/FileUpload.js';
		return sprintf(file_get_contents($filePath),
			$basePrefix,
			$this->elementId,
			BackendUtility::getModuleUrl('user_MediaM1'),
			$this->getAllowedExtensions(),
			GeneralUtility::getMaxUploadFileSize() * 1024,
			$this->getMaximumUploadLabel(),
			$this->getStorageService()->findCurrentStorage()->getUid()
		);
	}

	/**
	 * Returns the max upload file size in Mo.
	 *
	 * @return string
	 */
	protected function getMaximumUploadLabel() {
		$result = round(GeneralUtility::getMaxUploadFileSize() / 1024, 2);
		$label = LocalizationUtility::translate('max_upload_file', 'media');
		$result = sprintf($label, $result);
		return $result;
	}

	/**
	 * Get allowed extension.
	 *
	 * @return string
	 */
	 protected function getAllowedExtensions() {
		return implode("','", PermissionUtility::getInstance()->getAllowedExtensions());
	}

	/**
	 * Compute the base prefix by removing the square brackets.
	 *
	 * @param string $prefix
	 * @return string
	 */
	protected function getBasePrefix($prefix) {
		$parts = explode('[', $prefix);
		return empty($parts) ? '' : $parts[0];
	}

	/**
	 * Returns additional file info.
	 *
	 * @return string
	 */
	protected function getFileInfo() {
		return ''; // empty return here but check out Tceforms/FileUpload
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\StorageService
	 */
	protected function getStorageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\StorageService');
	}

}
