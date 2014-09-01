<?php
namespace TYPO3\CMS\Media\View\Button;

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
use TYPO3\CMS\Media\Module\Parameter;
use TYPO3\CMS\Vidi\View\AbstractComponentView;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Module\ModulePlugin;

/**
 * View which renders a "image-editor" button to be placed in the grid.
 */
class ImageEditorButton extends AbstractComponentView {

	/**
	 * Renders a "image-editor" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('imageEditor')) {
			$result = sprintf('<a href="%s" class="btn-imageEditor" data-uid="%s" title="%s">%s</a>',
				$this->getImageEditorUri($object),
				$object->getUid(),
				LocalizationUtility::translate('edit_image', 'media'),
				IconUtility::getSpriteIcon('extensions-media-image-edit')
			);
		}
		return $result;
	}

	/**
	 * @param Content $object
	 * @return string
	 */
	protected function getImageEditorUri(Content $object) {
		$urlParameters = array(
			Parameter::PREFIX => array(
				'controller' => 'ImageEditor',
				'action' => 'show',
				'file' => $object->getUid(),
			),
		);
		return BackendUtility::getModuleUrl(Parameter::MODULE_SIGNATURE, $urlParameters);
	}
}
