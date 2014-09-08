<?php
namespace TYPO3\CMS\Media\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Media\Utility\ImagePresetUtility;

/**
 * View helper which returns default preset values related to an image dimension
 */
class ImageDimensionViewHelper extends AbstractViewHelper {

	/**
	 * Returns preset values related to an image dimension
	 *
	 * @param string $preset
	 * @param string $dimension
	 * @return int
	 */
	public function render($preset, $dimension = 'width') {
		$imageDimension = ImagePresetUtility::getInstance()->preset($preset);
		if ($dimension == 'width') {
			$result = $imageDimension->getWidth();
		} else {
			$result = $imageDimension->getHeight();
		}
		return $result;
	}
}
