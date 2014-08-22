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
use TYPO3\CMS\Vidi\View\AbstractComponentView;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Vidi\Module\ModulePlugin;

/**
 * View helper which renders a "file picker" menu item to be placed in the grid menu for Media.
 */
class FilePickerMenuItem extends AbstractComponentView {

	/**
	 * Renders a "file picker" menu item to be placed in the grid menu for Media.
	 *
	 * @return string
	 */
	public function render() {
		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('filePicker')) {
			$result = sprintf('<li><a href="%s" class="mass-file-picker" data-argument="assets">%s Insert files</a>',
				$this->renderMassDeleteUri(),
				IconUtility::getSpriteIcon('extensions-media-image-export')
			);
		}
		return $result;
	}

	/**
	 * Render a mass delete URI.
	 *
	 * @return string
	 */
	public function renderMassDeleteUri() {

		return sprintf('%s&%s[format]=json&%s[action]=massDelete&%s[controller]=Asset',
			BackendUtility::getModuleUrl(ModuleUtility::getModuleSignature()),
			ModuleUtility::getParameterPrefix(),
			ModuleUtility::getParameterPrefix(),
			ModuleUtility::getParameterPrefix()
		);
	}
}
