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

/**
 * View helper which render an icon using sprites
 */
class SpriteViewHelper extends AbstractViewHelper {

	/**
	 * Returns an icon using sprites
	 *
	 * @param string $name the file to include
	 * @return string
	 */
	public function render($name = NULL) {
		return \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon($name);
	}

}
