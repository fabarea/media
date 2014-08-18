<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;

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
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 * View helper which renders a "move" menu item to be placed in the grid menu for Media.
 */
class MenuItemChangeStorageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders a "move" menu item to be placed in the grid menu for Media.
	 *
	 * @return string
	 */
	public function render() {
		return sprintf('<li><a href="%s" class="change-storage" >%s Change Storage</a>',
			$this->renderChangeStorageUri(),
			IconUtility::getSpriteIcon('extensions-media-storage-change')
		);
	}

	/**
	 * Render a mass delete URI.
	 *
	 * @return string
	 */
	public function renderChangeStorageUri() {
		return sprintf('%s',
			BackendUtility::getModuleUrl(ModuleUtility::getModuleSignature())
		);
	}
}
