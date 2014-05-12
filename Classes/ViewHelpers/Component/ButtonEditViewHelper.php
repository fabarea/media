<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Media\Domain\Model\Asset;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * View helper which renders a "edit" button to be placed in the grid.
 */
class ButtonEditViewHelper extends AbstractViewHelper {

	/**
	 * Renders a "edit" button to be placed in the grid.
	 *
	 * @param Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($object);
		$metadataProperties = $asset->_getMetaData();

		return sprintf('<a href="%s" data-uid="%s" class="btn-edit" title="%s">%s</a>',
			$this->getUri($asset),
			$metadataProperties['uid'],
			LocalizationUtility::translate('edit_metadata', 'media'),
			IconUtility::getSpriteIcon('actions-document-open')
		);
	}

	/**
	 * @param Asset $asset
	 * @return string
	 */
	public function getUri(Asset $asset){
		$metadataProperties = $asset->_getMetaData();
		$returnUrl = rawurlencode(BackendUtility::getModuleUrl(GeneralUtility::_GP('M')));
		return sprintf('alt_doc.php?edit[%s][%s]=edit',
			'sys_file_metadata',
			$metadataProperties['uid']
		) . '&returnUrl=' . $returnUrl;
	}
}
