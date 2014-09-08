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

use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\View\AbstractComponentView;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * View which renders a "delete" button to be placed in the grid.
 */
class DeleteButton extends AbstractComponentView {

	/**
	 * Renders a "delete" button to be placed in the grid.
	 *
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$result = '';

		// Only display the delete icon if the file has no reference.
		if ($this->getFileReferenceService()->countTotalReferences($object->getUid()) === 0) {

			$result = sprintf('<a href="%s" class="btn-delete" data-uid="%s">%s</a>',
				$this->getDeleteUri($object),
				$object->getUid(),
				IconUtility::getSpriteIcon('actions-edit-delete')
			);
		}
		return $result;
	}

	/**
	 * @param Content $object
	 * @return string
	 */
	protected function getDeleteUri(Content $object) {
		$additionalParameters = array(
			$this->getModuleLoader()->getParameterPrefix() => array(
				'controller' => 'Content',
				'action' => 'delete',
				'format' => 'json',
				'matches' => array(
					'uid' => $object->getUid(),
				),
			),
		);
		return $this->getModuleLoader()->getModuleUrl($additionalParameters);
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\FileReferenceService
	 */
	protected function getFileReferenceService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\FileReferenceService');
	}

}
