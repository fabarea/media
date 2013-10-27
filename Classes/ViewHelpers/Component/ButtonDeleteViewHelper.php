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
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 * View helper which renders a "delete" button to be placed in the grid
 */
class ButtonDeleteViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders a "delete" button to be placed in the grid.
	 *
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @return string
	 */
	public function render(\TYPO3\CMS\Vidi\Domain\Model\Content $object = NULL) {
		$result = '';
		if ($this->hasFileNoReferences($object)) {
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
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
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
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}

?>