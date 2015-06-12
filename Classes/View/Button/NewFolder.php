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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a button to create a new folder.
 */
class NewFolder extends AbstractComponentView {

	/**
	 * Renders a button to create a new folder.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$output = '';
		if ($this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin()) {
			$output = sprintf('<div style="float: left;"><a href="%sfile_newfolder.php?target=%s&amp;returnUrl=%s" title="%s">%s</a></div>',
				$GLOBALS['BACK_PATH'],
				$this->getCombineIdentifier(),
				$this->getReturnUrl(),
				$this->getLabel(),
				IconUtility::getSpriteIcon('actions-document-new')
			);
		}
		return $output;
	}

	/**
	 * @return string
	 */
	protected function getLabel() {
		$label = $this->getLanguageService()->sL('LLL:EXT:lang/locallang_core.xlf:cm.new', TRUE);
		return $this->getLanguageService()->makeEntities($label);
	}

	/**
	 * @return string
	 */
	protected function getCombineIdentifier() {
		$combineIdentifier = $this->getMediaModule()->getCombinedIdentifier();
		return rawurlencode($combineIdentifier);
	}

	/**
	 * @return string
	 */
	protected function getReturnUrl() {
		$returnUrl = BackendUtility::getModuleUrl(
			GeneralUtility::_GP('M'),
			$this->getAdditionalParameters()
		);
		return rawurlencode($returnUrl);
	}

	/**
	 * @return array
	 */
	protected function getAdditionalParameters() {

		$additionalParameters = array();
		if (GeneralUtility::_GP('id')) {
			$additionalParameters = array(
				'id' => urldecode(GeneralUtility::_GP('id')),
			);
		}
		return $additionalParameters;
	}

	/**
	 * @return MediaModule
	 */
	protected function getMediaModule() {
		return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
	}

	/**
	 * @return \TYPO3\CMS\Lang\LanguageService
	 */
	protected function getLanguageService() {
		return $GLOBALS['LANG'];
	}

}
