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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Media\Utility\Path;
use TYPO3\CMS\Vidi\ModulePlugin;

/**
 * View helper which renders content for link creator plugin.
 */
class PluginLinkCreatorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders a hidden link for link creator.
	 *
	 * @return string
	 */
	public function render() {
		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('linkCreator')) {
			$result = sprintf('<script type="text/javascript" src="%s"></script>
			<a href="%s" id="btn-linkCreator-current" class="btn btn-linkCreator" style="display: none"></a>',
				Path::getRelativePath('JavaScript/Media.Plugin.LinkCreator.js'),
				ModuleUtility::getUri('show', 'LinkCreator')
			);
		};
		return $result;
	}
}
