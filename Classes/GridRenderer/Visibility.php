<?php
namespace TYPO3\CMS\Media\GridRenderer;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * Class rendering visibility for the Grid.
 */
class Visibility extends \TYPO3\CMS\Vidi\GridRenderer\GridRendererAbstract {

	/**
	 * Render visibility for the Grid.
	 *
	 * @return string
	 */
	public function render() {
		$spriteName = $this->object->getVisible() ? 'actions-edit-unhide' : 'actions-edit-hide';
		$result = \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon($spriteName);
		return $result;
	}
}
?>