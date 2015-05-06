<?php
namespace Fab\Media\Backend;

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
use Fab\Media\Module\ModuleParameter;

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
		$cssFile = ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Build/media_tce.min.css';
		$this->pageRenderer->addCssFile($cssFile);

		// language labels for JavaScript files
		$this->pageRenderer->addInlineLanguageLabelFile(ExtensionManagementUtility::extPath('media') . 'Resources/Private/Language/locallang.xlf', 'media_file_upload');

		// js files to be loaded
		$this->pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('lang') . 'Resources/Public/JavaScript/Typo3Lang.js');
		$this->pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Build/media_tce.min.js');
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

		/** @var $fileUpload \Fab\Media\Form\FileUploadTceForms */
		$fileUpload = GeneralUtility::makeInstance('Fab\Media\Form\FileUploadTceForms');
		$fileUpload->setValue($fileMetadataRecord['file'])->setPrefix(ModuleParameter::PREFIX);
		return $fileUpload->render();
	}
}
