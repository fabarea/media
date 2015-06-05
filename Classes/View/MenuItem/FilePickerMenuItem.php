<?php
namespace Fab\Media\View\MenuItem;

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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Module\ModulePlugin;

/**
 * View which renders a "file picker" menu item to be placed in the grid menu for Media.
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
				$this->getMassDeleteUri(),
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
	protected function getMassDeleteUri() {
		$urlParameters = array(
			MediaModule::getParameterPrefix() => array(
				'controller' => 'Asset',
				'action' => '',
				'format' => 'json',
			),
		);
		return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
	}

}
