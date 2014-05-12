<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
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
