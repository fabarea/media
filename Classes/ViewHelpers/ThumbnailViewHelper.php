<?php
namespace TYPO3\CMS\Media\ViewHelpers;
/***************************************************************
*  Copyright notice
*
*  (c) 2012
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * View helper which returns a configurable thumbnail of an Asset
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class ThumbnailViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Returns a configurable thumbnail of an asset
	 *
	 * @param object $object
	 * @param array $configuration
	 * @param array $attributes DOM attributes to add to the thumbnail image
	 * @param boolean $wrap whether the thumbnail should be wrapped with an anchor tag. (OBSOLETE WILL BE REMOVED IN MEDIA 1.2!)
	 * @param string $preset an image dimension preset
	 * @param string $output an image dimension preset. Can be: uri, image, imageWrapped
	 * @param array $configurationWrap the configuration given to the wrap
	 * @return string
	 */
	public function render($object, $configuration = array(), $attributes = array(), $wrap = FALSE, $preset = NULL,
	                       $output = 'image', $configurationWrap = array()) {

		/** @var $object \TYPO3\CMS\Media\Domain\Model\Asset */
		if ($preset) {
			$imageDimension = \TYPO3\CMS\Media\Utility\ImagePresetUtility::getInstance()->preset($preset);
			$configuration['width'] = $imageDimension->getWidth();
			$configuration['height'] = $imageDimension->getHeight();
		}

		// @todo remove me as of Media 1.2
		if ($wrap) {
			$output = \TYPO3\CMS\Media\Service\ThumbnailInterface::OUTPUT_IMAGE_WRAPPED;
		}

		/** @var $thumbnailService \TYPO3\CMS\Media\Service\ThumbnailService */
		$thumbnailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService');
		return $thumbnailService->setFile($object)
			->setConfiguration($configuration)
			->setConfigurationWrap($configurationWrap)
			->setAttributes($attributes)
			->setOutputType($output)
			->create();
	}
}
?>