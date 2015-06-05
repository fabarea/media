<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Fabien Udriot <fabien.udriot@ecodev.ch>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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
use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Link Creator plugin for htmlArea RTE
 */
class tx_rtehtmlarea_linkcreator extends \TYPO3\CMS\Rtehtmlarea\RteHtmlAreaApi {

	protected $extensionKey = 'media'; // The key of the extension that is extending htmlArea RTE
	protected $pluginName = 'LinkCreator'; // The name of the plugin registered by the extension
	protected $relativePathToSkin = 'Resources/Public/HtmlArea/LinkCreator/HtmlArea.css';// Path to the skin (css) file relative to the extension dir.

	protected $pluginButtons = 'linkcreator';
	protected $convertToolbarForHtmlAreaArray = array (
		'linkcreator' => 'LinkCreator', #must be the same in the javascript var buttonId = LinkCreator
	);

	 /**
	 * Return JS configuration of the htmlArea plugins registered by the extension
	 *
	 * @param integer $RTEcounter relative id of the RTE editing area in the form
	 * @return string JS configuration for registered plugins
	 *
	 * The returned string will be a set of JS instructions defining the configuration that will be provided to the plugin(s)
	 * Each of the instructions should be of the form:
	 * 	RTEarea['.$RTEcounter.']["buttons"]["button-id"]["property"] = "value";
	 */
	public function buildJavascriptConfiguration($RTEcounter) {
		$registerRTEinJavascriptString = '';
		$button = 'linkcreator';
		if (in_array($button, $this->toolbar)) {
			if (!is_array($this->thisConfig['buttons.']) || !is_array($this->thisConfig['buttons.'][($button . '.')])) {
				$registerRTEinJavascriptString .= '
			RTEarea[' . $RTEcounter . '].buttons.' . $button . ' = new Object();';
			}
			$registerRTEinJavascriptString .= '
			RTEarea[' . $RTEcounter . '].buttons.' . $button . '.pathLinkModule = ' . GeneralUtility::quoteJSvalue($this->getModuleUrl());
		}
		return $registerRTEinJavascriptString;
	}

	/**
	 * @return string
	 */
	protected function getModuleUrl() {
		$moduleSignature = VidiModule::getSignature();
		return BackendUtility::getModuleUrl($moduleSignature);
	}
}