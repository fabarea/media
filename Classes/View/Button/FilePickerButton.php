<?php
namespace Fab\Media\View\Button;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Media\Module\ModuleParameter;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;
use Fab\Vidi\Module\ModulePlugin;

/**
 * View which renders a "file-picker" button to be placed in the grid.
 */
class FilePickerButton extends AbstractComponentView {

	/**
	 * Renders a "file-picker" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('filePicker')) {
			$result = sprintf('<a href="%s" class="btn-filePicker" data-uid="%s" title="%s">%s</a>',
				$this->getFilePickerUri($object),
				$object->getUid(),
				LocalizationUtility::translate('edit_image', 'media'),
				IconUtility::getSpriteIcon('extensions-media-image-export')
			);
		}
		return $result;
	}

	/**
	 * @param Content $object
	 * @return string
	 */
	protected function getFilePickerUri(Content $object) {
		$urlParameters = array(
			ModuleParameter::PREFIX => array(
				'controller' => 'Asset',
				'action' => 'download',
				'file' => $object->getUid(),
			),
		);
		return BackendUtility::getModuleUrl(ModuleParameter::MODULE_SIGNATURE, $urlParameters);
	}
}
