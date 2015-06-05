<?php
namespace Fab\Media\Grid;

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

use Fab\Vidi\Grid\GridRendererAbstract;

/**
 * Class rendering permission in the grid.
 */
class FrontendPermissionRenderer extends GridRendererAbstract {

	/**
	 * Render permission in the grid.
	 *
	 * @return string
	 */
	public function render() {
		$result = '';

		$frontendUserGroups = $this->object['metadata']['fe_groups'];
		if (!empty($frontendUserGroups)) {

			/** @var $frontendUserGroup \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup */
			foreach ($frontendUserGroups as $frontendUserGroup) {
				$result .= sprintf('<li style="list-style: disc">%s</li>', $frontendUserGroup['title']);
			}
			$result = sprintf('<ul>%s</ul>', $result);
		}
		return $result;
	}
}
