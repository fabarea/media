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

		// Load StyleSheet in the Page Renderer
		$this->pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();
		$cssFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/StyleSheet/FileUploader/fineuploader.tceforms.css';
		$this->pageRenderer->addCssFile($cssFile);

		// js files to be loaded
		$jsFiles = array(
			'Resources/Public/JavaScript/JQuery/jquery.fineuploader.compatibility.js',
			'Resources/Public/JavaScript/JQuery/jquery.fineuploader-3.4.1.js',
		);

		foreach ($jsFiles as $file) {
			$this->pageRenderer->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('media') . $file);

		}
	}

	/**
	 * This method renders the user friendly upload widget.
	 * @see http://fineuploader.com/
	 *
	 * @param array $PA: information related to the field
	 * @param Object $tceForms: reference to calling TCEforms object
	 * @return	string	The HTML for the form field
	 */
	public function renderFileUpload($PA, $tceForms) {

		// Instantiate Template Engine
		/* @var $view \TYPO3\CMS\Fluid\View\StandaloneView */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView');

		// Get template file and pass it to the view
		$filePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('media') . 'Resources/Private/Templates/ViewHelpers/Form/TceForms/FileUpload.html';
		$view->setTemplatePathAndFilename($filePath);
		return $view->render();
	}
}

?>