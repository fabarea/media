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

use TYPO3\CMS\Media\Utility\Path;
use TYPO3\CMS\Vidi\ModulePlugin;

/**
 * View helper which renders content for file picker plugin.
 */
class PluginFilePickerViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders a hidden link for file picker.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';
		if (ModulePlugin::getInstance()->isPluginRequired('filePicker')) {

			$result = sprintf('<script type="text/javascript" src="%s"></script>',
				Path::getRelativePath('JavaScript/Media.Plugin.FilePicker.js')
			);
		};
		return $result;
	}
}
