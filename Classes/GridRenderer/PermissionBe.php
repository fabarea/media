<?php
namespace TYPO3\CMS\Media\GridRenderer;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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
 * Class rendering permission in the grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class PermissionBe extends \TYPO3\CMS\Vidi\GridRenderer\GridRendererAbstract {

	/**
	 * Render permission in the grid.
	 *
	 * @return string
	 */
	public function render() {

		$asset = \TYPO3\CMS\Media\ObjectFactory::getInstance()->convertContentObjectToAsset($this->object);

		$result = '';

		// We are force to convert to array to be sure the result exists.
		// Method "isValid" from QueryResult can not be used here (returns TRUE only once?).
		$backendUserGroups = $asset->getBackendUserGroups()->toArray();
		if (!empty($backendUserGroups)) {
			$template = '<li style="list-style: disc">%s</li>';
			/** @var $backendUserGroup \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup */
			foreach ($asset->getBackendUserGroups() as $backendUserGroup) {
				$result .= sprintf($template, $backendUserGroup->getTitle());
			}
		}

		return $result;
	}
}
?>