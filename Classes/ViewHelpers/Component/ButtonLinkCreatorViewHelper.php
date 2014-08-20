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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Module\ModulePlugin;

/**
 * View helper which renders a "link-creator" button to be placed in the grid.
 */
class ButtonLinkCreatorViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Vidi\ViewHelpers\Uri\EditViewHelper
	 * @inject
	 */
	protected $uriEditViewHelper;

	/**
	 * Renders a "link-creator" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('linkCreator')) {
			$result = sprintf('<a href="%s&%s[asset]=%s" class="btn-linkCreator" data-uid="%s" title="%s">%s</a>',
				ModuleUtility::getUri('show', 'LinkCreator'),
				ModuleUtility::getParameterPrefix(),
				$object->getUid(),
				$object->getUid(),
				LocalizationUtility::translate('create_link', 'media'),
				IconUtility::getSpriteIcon('apps-pagetree-page-shortcut-external-root')
			);
		}
		return $result;
	}
}
