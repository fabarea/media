<?php
namespace TYPO3\CMS\Media\ViewHelpers\Table;

/***************************************************************
*  Copyright notice
*
*  (c) 2012
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
/**
 * View helper which displays a checkbox for a row
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class CheckboxViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Generates a checkbox.
	 *
	 * @param int $index
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $object
	 * @return string the value
	 * @api
	 */
	public function render($index, $object) {
		return sprintf('<input type="checkbox" class="checkbox-row" data-index="%s" data-uid="%s"/>',
			$index,
			$object->getUid()
		);
	}
}
?>