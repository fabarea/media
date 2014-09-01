<?php
namespace TYPO3\CMS\Media\View\MenuItem;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Media\Module\Parameter;
use TYPO3\CMS\Vidi\View\AbstractComponentView;

/**
 * View which renders a "mass delete" menu item to be placed in the grid menu for Media.
 */
class MassDeleteMenuItem extends AbstractComponentView {

	/**
	 * Renders a "mass delete" menu item to be placed in the grid menu for Media.
	 *
	 * @return string
	 */
	public function render() {
		return sprintf('<li><a href="%s" class="mass-delete-assets" data-argument="assets">%s Delete</a>',
			$this->getMassDeleteUri(),
			IconUtility::getSpriteIcon('actions-edit-delete')
		);
	}

	/**
	 * @return string
	 */
	protected function getMassDeleteUri() {
		$urlParameters = array(
			Parameter::PREFIX => array(
				'controller' => 'Asset',
				'action' => 'massDelete',
				'format' => 'json'
			),
		);
		return BackendUtility::getModuleUrl(Parameter::MODULE_SIGNATURE, $urlParameters);
	}

}
