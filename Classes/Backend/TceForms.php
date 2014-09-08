<?php
namespace TYPO3\CMS\Media\Backend;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Module\ModuleParameter;

/**
 * Custom fields for Media
 */
class TceForms {

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 */
	protected $pageRenderer;

	/**
	 * Constructor
	 */
	public function __construct() {

		// Load StyleSheets in the Page Renderer
		$this->pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();
		$cssFile = ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/StyleSheets/FileUploader/fineuploader.tceforms.css';
		$this->pageRenderer->addCssFile($cssFile);

		// language labels for JavaScript files
		$this->pageRenderer->addInlineLanguageLabelFile(ExtensionManagementUtility::extPath('media') . 'Resources/Private/Language/locallang.xlf', 'media_file_upload');

		// js files to be loaded
		$jsFiles = array(
			ExtensionManagementUtility::extRelPath('lang') . 'Resources/Public/JavaScript/Typo3Lang.js',
			'Resources/Public/JavaScript/JQuery/jquery.fineuploader.compatibility.js',
			'Resources/Public/JavaScript/JQuery/jquery.fineuploader-3.4.1.js',
			'Resources/Public/JavaScript/Encoder.js',
		);

		foreach ($jsFiles as $file) {
			$this->pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('media') . $file);

		}
	}

	/**
	 * This method renders the user friendly upload widget.
	 *
	 * @param array $propertyArray : information related to the field
	 * @param Object $tceForms : reference to calling TCEforms object
	 * @throws \Exception
	 * @return string
	 */
	public function renderFileUpload($propertyArray, $tceForms) {

		$fileMetadataRecord = $propertyArray['row'];

		if ($fileMetadataRecord['file'] <= 0) {
			throw new \Exception('I could not find a valid file identifier', 1392926871);
		}

		/** @var $fileUpload \TYPO3\CMS\Media\Form\FileUploadTceForms */
		$fileUpload = GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\FileUploadTceForms');
		$fileUpload->setValue($fileMetadataRecord['file'])->setPrefix(ModuleParameter::PREFIX);
		return $fileUpload->render();
	}
}
