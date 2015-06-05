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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a "edit" button to be placed in the grid.
 */
class EditButton extends AbstractComponentView {

	/**
	 * Renders a "edit" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$file = $this->getFileConverter()->convert($object);
		$metadataProperties = $file->_getMetaData();

		return sprintf('<a href="%s" data-uid="%s" class="btn-edit" title="%s">%s</a>',
			$this->getUri($file),
			$metadataProperties['uid'],
			LocalizationUtility::translate('edit_metadata', 'media'),
			IconUtility::getSpriteIcon('actions-document-open')
		);
	}

	/**
	 * @param File $file
	 * @return string
	 */
	protected function getUri(File $file) {
		$metadataProperties = $file->_getMetaData();

		$returnUrl = rawurlencode(BackendUtility::getModuleUrl(GeneralUtility::_GP('M'), $this->getAdditionalParameters()));
		return sprintf('alt_doc.php?edit[%s][%s]=edit',
			'sys_file_metadata',
			$metadataProperties['uid']
		) . '&returnUrl=' . $returnUrl;
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
	 * @return \Fab\Media\TypeConverter\ContentToFileConverter
	 */
	protected function getFileConverter() {
		return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
	}

}
