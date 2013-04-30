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
	 * @param boolean $wrap whether the thumbnail should be wrapped with an anchor tag.
	 * @param string $preset an image dimension preset
	 * @return string
	 */
	public function render($object, $configuration = array(), $attributes = array(), $wrap = FALSE, $preset = NULL) {

		/** @var $object \TYPO3\CMS\Media\Domain\Model\Asset */
		if ($preset) {
			$imageDimension = \TYPO3\CMS\Media\Utility\SettingImagePreset::getInstance()->preset($preset);
			$configuration['width'] = $imageDimension->getWidth();
			$configuration['height'] = $imageDimension->getHeight();
		}

		/** @var $thumbnailSpecification \TYPO3\CMS\Media\Service\ThumbnailSpecification */
		$thumbnailSpecification = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailSpecification');
		$thumbnailSpecification->setConfiguration($configuration)
			->setAttributes($attributes)
			->setWrap($wrap);

		return $object->getThumbnail($thumbnailSpecification);
	}
}

?>