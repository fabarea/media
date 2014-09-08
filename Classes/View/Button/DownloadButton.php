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
use TYPO3\CMS\Media\Module\ModuleParameter;
use TYPO3\CMS\Vidi\View\AbstractComponentView;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * View which renders a "download" button to be placed in the grid.
 */
class DownloadButton extends AbstractComponentView {

	/**
	 * Renders a "download" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {

		$result = sprintf(
			'<a href="%s" data-uid="%s" class="btn-download" title="%s">%s</a>',
			$this->getDownloadUri($object),
			$object->getUid(),
			LocalizationUtility::translate('download', 'media'),
			IconUtility::getSpriteIcon('actions-system-extension-download')
		);

		return $result;
	}

	/**
	 * @param Content $object
	 * @return string
	 */
	protected function getDownloadUri(Content $object) {
		$urlParameters = array(
			ModuleParameter::PREFIX => array(
				'controller' => 'Asset',
				'action' => 'download',
				'file' => $object->getUid(),
			),
		);
		return BackendUtility::getModuleUrl(ModuleParameter::MODULE_SIGNATURE, $urlParameters);
	}

}
