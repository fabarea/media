<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011
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
 * TCEform custom field for Media
 *
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Media_TCEforms_UserField {

	/**
	 * The extension key
	 *
	 * @var string
	 */
	protected $extKey = 'media';

	/**
	 * @var t3lib_file_Domain_Repository_MountRepository
	 */
	protected $mountRepository;

	/**
	 * @var t3lib_file_Domain_Model_Mount
	 */
	protected $mount;

	/**
	 * The absolute Icon path
	 *
	 * @var string
	 */
	protected $thumbnailIconPath;

	/**
	 * The public Icon path
	 *
	 * @var string
	 */
	protected $thumbnailPublicIconPath;

	/**
	 * Constructor
	 */
	public function __construct() {

			// Load preferences
		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->thumbnailIconPath = t3lib_extMgm::extPath('media') . 'Resources/Public/Icons/MimeTypes/';
		$this->thumbnailIconPublicPath = t3lib_extMgm::extRelPath('media') . 'Resources/Public/Icons/MimeTypes/';

			// Instantiate necessary stuff for FAL
		$this->mountRepository = t3lib_div::makeInstance('t3lib_file_Domain_Repository_MountRepository');
		$this->mount = $this->mountRepository->findByUid($this->configuration['storage']);

			// Load StyleSheet in the Page Renderer
		$this->pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();
		$cssFile = t3lib_extMgm::extRelPath('media') . 'Resources/Public/StyleSheets/Media.css';
		$this->pageRenderer->addCssFile($cssFile);
	}

	/**
	 * This method renders the user-defined thumbnails for Media purpose
	 *
	 * @param	array			$PA: information related to the field
	 * @param	t3lib_tceforms	$fobj: reference to calling TCEforms object
	 *
	 * @return	string	The HTML for the form field
	 */
	public function renderFile ($PA, t3lib_TCEforms $fobj) {

			// Instantiate Template Engine
		/* @var $view Tx_Fluid_View_StandaloneView */
		$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');

			// Get template file and pass it to the view
		$filePath = t3lib_extMgm::extPath('media') . 'Resources/Private/TCEforms/File.html';
		$view->setTemplatePathAndFilename($filePath);

		$record = $PA['row'];

		if ($record['file'] > 0) {

				// TRUE means this is an image and a thumbnail can be generated
			if ($record['media_type'] == 2) {
				$fileRepository = t3lib_div::makeInstance('t3lib_file_Domain_Repository_FileRepository');
				$file = $fileRepository->findByUid($record['file']);

					// Fetches the absolute file path
				$fileAbsolutePath = $this->mount->getDriver()->getAbsolutePath($file);

					// Generates HTML for Thumbnail generation
				$thumbnail = t3lib_BEfunc::getThumbNail('thumbs.php', $fileAbsolutePath,' align="middle" style="border:solid 1px #ccc;" class="tx-media-thumbnail" ',160);
			}
			else {
				$thumbnailIcon = $this->thumbnailIconPath . $record['mime_type'] . 'png';
					// Makes sure the thumbnail exists
				if (file_exists($thumbnailIcon)) {
					$thumbnailIcon = $this->thumbnailIconPublicPath . $record['mime_type'] . 'png';
				}
				else {
					$thumbnailIcon = $this->thumbnailIconPublicPath . 'unknown.png';
				}
				$thumbnail = '<img src="' . $thumbnailIcon. '" alt="icon" />';
			}
		}

			// Assignes values for the View
		$fileName = $file ? $file->getName() : '';
		$publicUrl = $file ? $this->mount->getDriver()->getPublicUrl($file) : '';
		$thumbnail = isset($thumbnail) ? $thumbnail : '';

		$view->assign('fileName', $fileName);
		$view->assign('publicUrl', $publicUrl);
		$view->assign('thumbnail', $thumbnail);
		$view->assign('uploadMaxFilesize', ini_get('upload_max_filesize'));
		$view->assign('mimeTypeAllowed', $this->configuration['mime_type_allowed']);

		return $view->render();
	}

	/**
	 * This method renders the user-defined thumbnails for Media purpose
	 *
	 * @param	array			$PA: information related to the field
	 * @param	t3lib_tceforms	$fobj: reference to calling TCEforms object
	 *
	 * @return	string	The HTML for the form field
	 */
	public function renderThumbnail($PA, t3lib_TCEforms $fobj) {

			// Instantiate Template Engine
		/* @var $view Tx_Fluid_View_StandaloneView */
		$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');

			// Get template file and pass it to the view
		$filePath = t3lib_extMgm::extPath('media') . 'Resources/Private/TCEforms/Thumbnail.html';
		$view->setTemplatePathAndFilename($filePath);

		$record = $PA['row'];
		
		if ($record['thumbnail'] > 0) {
//			t3lib_utility_Debug::debug(123, '123');
//			exit();
		}
			// Assign template variables
		$view->assign('uploadMaxFilesize', ini_get('upload_max_filesize'));

		return $view->render();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media/Resources/Private/PHP/TCEforms/class.tx_media_tceforms.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media/Resources/Private/PHP/TCEforms/class.tx_media_tceforms.php']);
}

?>