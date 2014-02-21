<?php
namespace TYPO3\CMS\Media\Backend;
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
use TYPO3\CMS\Media\Utility\ModuleUtility;

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

		// js files to be loaded
		$jsFiles = array(
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
		$fileUpload->setValue($fileMetadataRecord['file'])->setPrefix(ModuleUtility::getParameterPrefix());
		return $fileUpload->render();
	}
}
