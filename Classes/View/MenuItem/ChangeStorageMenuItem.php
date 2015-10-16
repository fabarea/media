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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a "move" menu item to be placed in the "change storage" menu.
 */
class ChangeStorageMenuItem extends AbstractComponentView {

	/**
	 * Renders a "change storage" menu item to be placed in the grid menu of Media.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';
		if (!$this->getMediaModule()->hasFolderTree()) {
			$output = sprintf('<li><a href="%s" class="change-storage" >%s %s</a>',
				$this->getChangeStorageUri(),
				IconUtility::getSpriteIcon('extensions-media-storage-change'),
				LocalizationUtility::translate('change_storage', 'media')
			);
		}
		return $output;
	}

	/**
	 * @return string
	 */
	protected function getChangeStorageUri() {
		$urlParameters = array(
			MediaModule::getParameterPrefix() => array(
				'controller' => 'Asset',
				'action' => 'editStorage',
			),
		);
		return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
	}

	/**
	 * @return MediaModule
	 */
	protected function getMediaModule() {
		return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
	}

}
