<?php
namespace TYPO3\CMS\Media\Form;

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
 * A class to render a file upload widget.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class FileUpload extends \TYPO3\CMS\Media\Form\AbstractFormField  {

	/**
	 * @var string
	 */
	protected $elementId;

	/**
	 * @var string
	 */
	protected $callBack;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File
	 */
	protected $fileObject;

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
			$this->fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileObject($this->getValue());
		}

		// @todo remove me if not used anymore after legacy upload is implemented.
//		if (! $this->getName()) {
//			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Missing value for property "name" for text field', 1356217712);
//		}

		$result = sprintf($this->template,
			$this->getThumbnail(),
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
		if ($this->fileObject) {

			/** @var $thumbnailService \TYPO3\CMS\Media\Service\Thumbnail */
			$thumbnailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\Thumbnail');
			$thumbnail = $thumbnailService
				->setFile($this->fileObject)
				->doWrap(TRUE)
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

		$filePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('media') . 'Resources/Private/Templates/ViewHelpers/Form/FileUpload/FileUpload.js';
		return sprintf(file_get_contents($filePath),
			$basePrefix,
			$this->getValue(),
			$this->elementId,
			$this->getAllowedExtension(),
			\TYPO3\CMS\Core\Utility\GeneralUtility::getMaxUploadFileSize() * 1024,
			$this->getCallBack()
		);
	}

	/**
	 * Get allowed extension.
	 *
	 * @return string
	 */
	 protected function getAllowedExtension() {
		$extensions = \TYPO3\CMS\Media\Utility\Permission::getInstance()->getAllowedExtensions();
		return implode("','", $extensions);
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
	 * @return string
	 */
	public function getCallBack() {
		if (is_null($this->callBack)) {
			$this->callBack = <<<EOF
			// Default callback
			if (responseJSON.uid) {
				$('#asset-uid').val(responseJSON.uid);
			}
			if (responseJSON.thumbnail) {
				// Replace thumbnail by new one.
				$(this).prev().html(Encoder.htmlDecode(responseJSON.thumbnail));
				$('.qq-upload-list', this).html('');
			}
			if (responseJSON.formAction) {
				$(this).closest('form').attr('action', (Encoder.htmlDecode(responseJSON.formAction)));
			}
EOF;
		}
		return "\n" . $this->callBack;
	}

	/**
	 * @param string $callBack
	 */
	public function setCallBack($callBack) {
		$this->callBack = $callBack;
	}

}
?>