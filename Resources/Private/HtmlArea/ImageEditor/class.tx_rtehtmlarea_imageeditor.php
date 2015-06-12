<?php

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

use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Image Editor plugin for htmlArea RTE
 */
class tx_rtehtmlarea_imageeditor extends \TYPO3\CMS\Rtehtmlarea\RteHtmlAreaApi {

	/**
	 * The key of the extension that is extending htmlArea RTE
	 *
	 * @var string
	 */
	protected $extensionKey = 'media';

	/**
	 * The name of the plugin registered by the extension
	 *
	 * @var string
	 */
	protected $pluginName = 'ImageEditor';

	/**
	 * Path to the skin (css) file relative to the extension dir.
	 *
	 * @var string
	 */
	protected $relativePathToSkin = 'Resources/Public/HtmlArea/ImageEditor/HtmlArea.css';

	/**
	 * @var string
	 */
	protected $pluginButtons = 'imageeditor';

	/**
	 * Must be the same in the javascript var buttonId = LinkCreator
	 *
	 * @var array
	 */
	protected $convertToolbarForHtmlAreaArray = array (
		'imageeditor' => 'ImageEditor',
	);

	/**
	 * Return JS configuration of the htmlArea plugins registered by the extension
	 *
	 * @param integer Relative id of the RTE editing area in the form
	 * @return string JS configuration for registered plugins
	 *
	 * The returned string will be a set of JS instructions defining the configuration that will be provided to the plugin(s)
	 * Each of the instructions should be of the form:
	 * 	RTEarea['.$RTEcounter.']["buttons"]["button-id"]["property"] = "value";
	 */
	public function buildJavascriptConfiguration($RTEcounter) {
		$registerRTEinJavascriptString = '';
		$button = 'imageeditor';
		if (in_array($button, $this->toolbar)) {
			if (!is_array($this->thisConfig['buttons.']) || !is_array($this->thisConfig['buttons.'][($button . '.')])) {
				$registerRTEinJavascriptString .= '
			RTEarea[' . $RTEcounter . '].buttons.' . $button . ' = new Object();';
			}
			$registerRTEinJavascriptString .= '
			RTEarea[' . $RTEcounter . '].buttons.' . $button . '.pathLinkModule = ' . $this->getModuleUrl();
		}

		return $registerRTEinJavascriptString;
	}

	/**
	 * @return string
	 */
	protected function getModuleUrl() {

		$additionalParameters = array(
			VidiModule::getParameterPrefix() => array(
				'plugins' => array(
					'imageEditor'
				),
				'matches' => array(
					'type' => 2,
				),
			),
		);

		$moduleUrl = BackendUtility::getModuleUrl(
			VidiModule::getSignature(),
			$additionalParameters
		);

		return GeneralUtility::quoteJSvalue($moduleUrl);
	}

}
