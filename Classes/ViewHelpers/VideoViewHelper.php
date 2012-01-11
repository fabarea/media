<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Steffen Ritter <steffen.ritter@typo3.org>
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * renders a video
 *
 * @author	Steffen Ritter <steffen.ritter@typo3.org>
 *
 */
class Tx_Media_ViewHelpers_VideoViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var array
	 */
	protected $typoScriptSetup;

	/**
	 * @var	t3lib_fe contains a backup of the current $GLOBALS['TSFE'] if used in BE mode
	 */
	protected $tsfeBackup;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;


	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->typoScriptSetup = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
	}

	/**
	 * main render function
	 *
	 * @param t3lib_file_FileInterface $video
	 * @param string $previewImage
	 * @param int $width
	 * @param int $height
	 * @param array $additionalAttributes
	 * @return string
	 */
	public function render(t3lib_file_FileInterface $video, $previewImage = '', $width = 600, $height = 400, array $additionalAttributes = array()) {
		if ($video instanceof t3lib_file_FileReference) {
			$video = $video->getOriginalFile();
		}

		/** @var Tx_Media_Service_Variants $variantsService */
		$variantsService = t3lib_div::makeInstance('Tx_Media_Service_Variants');
		$video = $variantsService->findOriginal($video);

		/** @var tslib_cObj $contentObject */
		$contentObject = t3lib_div::makeInstance('tslib_cObj');
		$contentObject->start($video->toArray());
		$configuration = $this->typoScriptSetup['tt_content.']['media.']['20.']['mimeConf.']['swfobject.'];

		$configuration['type'] = 'video';
		$configuration['preferFlashOverHtml5'] = false;

//		$configuration['width'] = $width;
//		$configuration['height'] = $height;

		$configuration['flashvars.']['autoPlay'] = false;
		
			// get video sources
		$configuration['sources'] = array();
		$formats = $variantsService->getAlternateFiles($video);
		foreach ($formats as $videoVariant) {
			$configuration['sources'][] = $videoVariant->getPublicUrl();
		}

			// find fallback for flash (mp4 or flv)
		$flashFallback = current($variantsService->getAlternateFiles($video, 'mp4,flv'));
		if ($flashFallback instanceof t3lib_file_File) {
			$configuration['file'] = $flashFallback->getPublicUrl();
		}

			// hand over additional attributes
		foreach ($additionalAttributes AS $key => $value) {
			$configuration['attributes.'][$key] = $value;
		}


		if (($thumbnailFile = $variantsService->getThumbnailForFile($video)) !== NULL) {
			$configuration['attributes.']['poster'] = $thumbnailFile->getPublicUrl();
		} elseif ($previewImage !== '') {
			$configuration['attributes.']['poster'] = t3lib_div::getFileAbsFileName($previewImage);
		}
		return $contentObject->SWFOBJECT($configuration);
	}

}

?>