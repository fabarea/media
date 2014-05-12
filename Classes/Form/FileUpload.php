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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Media\Service\ThumbnailInterface;
use TYPO3\CMS\Media\Utility\PermissionUtility;
use TYPO3\CMS\Media\Utility\StorageUtility;

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
	 * @return \TYPO3\CMS\Media\Form\FileUpload
	 */
	public function __construct() {
		// Example:
		// <input multiple = "false" name = "tx_media_user_mediam1[media][name]" type ="file" >
		// <input name = "file[upload][1][target]" value = "1:/user_upload/images/persons/" type = "hidden" >

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
			StorageUtility::getInstance()->getCurrentStorage()->getUid()
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

}
