<?php
namespace TYPO3\CMS\Media\Grid;
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


/**
 * Class rendering visibility for the Grid.
 */
class VisibilityRenderer extends \TYPO3\CMS\Vidi\Grid\GridRendererAbstract {

	/**
	 * Render visibility for the Grid.
	 *
	 * @return string
	 */
	public function render() {
		$spriteName = $this->object->getVisible() ? 'actions-edit-hide' : 'actions-edit-unhide';
		$result = \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon($spriteName);
		return $result;
	}
}
