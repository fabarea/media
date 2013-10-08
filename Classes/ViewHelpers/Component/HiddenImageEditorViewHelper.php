<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Media\Utility\Path;
use TYPO3\CMS\Vidi\ModulePlugin;

/**
 * View helper which renders a hidden link for image editor.
 */
class HiddenImageEditorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders a hidden link for image editor.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';
		if (ModulePlugin::getInstance()->isPluginCalled('imageEditor')) {

			$result = sprintf('<script type="text/javascript" src="%s"></script>
				<script type="text/javascript" src="%s"></script>
				<a href="%s" id="btn-imageEditor-current" class="btn btn-imageEditor" style="display: none"></a>',
				Path::getRelativePath('JavaScript/Media.Rte.ImageEditor.js'),
				Path::getRelativePath('JavaScript/Media.Rte.Popup.js'),
				ModuleUtility::getUri('imageEditor', 'Asset')
			);
		};
		return $result;
	}
}

?>