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

use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\ModulePlugin;

/**
 * View helper which renders a "image-editor" button to be placed in the grid.
 */
class ButtonImageEditorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Vidi\ViewHelpers\Uri\EditViewHelper
	 * @inject
	 */
	protected $uriEditViewHelper;

	/**
	 * Renders a "image-editor" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('imageEditor')) {
			$result = sprintf('<a href="%s&%s[asset]=%s" class="btn-imageEditor" data-uid="%s" title="%s">%s</a>',
				ModuleUtility::getUri('show', 'ImageEditor'),
				ModuleUtility::getParameterPrefix(),
				$object->getUid(),
				$object->getUid(),
				LocalizationUtility::translate('edit_image', 'media'),
				IconUtility::getSpriteIcon('extensions-media-image-edit')
			);
		}
		return $result;
	}
}
