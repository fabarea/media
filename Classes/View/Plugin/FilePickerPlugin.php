<?php
namespace Fab\Media\View\Plugin;

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

use Fab\Vidi\View\AbstractComponentView;
use Fab\Media\Utility\Path;

/**
 * View which renders content for file picker plugin.
 */
class FilePickerPlugin extends AbstractComponentView {

	/**
	 * Renders a hidden link for file picker.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';
		if ($this->getModuleLoader()->hasPlugin('filePicker')) {

			$result = sprintf('<script type="text/javascript" src="%s"></script>',
				Path::getRelativePath('JavaScript/Media.Plugin.FilePicker.js')
			);
		};
		return $result;
	}
}
