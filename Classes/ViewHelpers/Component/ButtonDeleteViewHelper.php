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
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * View helper which renders a "delete" button to be placed in the grid.
 */
class ButtonDeleteViewHelper extends AbstractViewHelper {

	/**
	 * Renders a "delete" button to be placed in the grid.
	 *
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @return string
	 */
	public function render(Content $object = NULL) {
		$result = '';

		$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($object);

		if ($this->hasFileNoReferences($object) && $this->hasNotSoftImageReferences($asset) && $this->hasNotSoftLinkReferences($asset)) {

			// check if the file has a reference
			$result = sprintf('<a href="%s&%s[asset]=%s" class="btn-delete" data-uid="%s">%s</a>',
				ModuleUtility::getUri('delete', 'Asset'),
				ModuleUtility::getParameterPrefix(),
				$object->getUid(),
				$object->getUid(),
				IconUtility::getSpriteIcon('actions-edit-delete')
			);
		}
		return $result;
	}

	/**
	 * Tell whether the file has references.
	 *
	 * @param Content $object
	 * @return boolean
	 */
	protected function hasFileNoReferences($object) {

		// Get the file references of the asset
		$references = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'*',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $object->getUid()
		);

		return empty($references);
	}

	/**
	 * Return whether the asset has no soft image references.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	public function hasNotSoftImageReferences($asset) {
		$softReferences = array();

		foreach ($asset->getVariants() as $variant) {

			// Get the file references of the asset.
			$_softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
				'recuid, tablename',
				'sys_refindex',
				'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $variant->getUid()
			);

			$softReferences = array_merge($softReferences, $_softReferences);
		}
		return empty($softReferences);
	}

	/**
	 * Return whether the asset has no soft link references.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	public function hasNotSoftLinkReferences($asset) {

		// Get the link references of the asset.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $asset->getUid()
		);

		return empty($softReferences);
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}

?>