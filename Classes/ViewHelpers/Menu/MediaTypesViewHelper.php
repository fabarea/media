<?php
namespace TYPO3\CMS\Media\ViewHelpers\Menu;
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
 * View helper which returns a list of possible media types to be displayed in the "new" menu.
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class MediaTypesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Return a list of possible media types to be displayed in the "new" menu.
	 *
	 * @return array
	 */
	public function render() {
		$mediaTypes = array();

		$typeFilter = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(
			',',
			\TYPO3\CMS\Media\Utility\Configuration::get('visible_media_type_in_new_menu')
		);

		$types = \TYPO3\CMS\Media\Tca\ServiceFactory::getFormService('sys_file')->getTypes();
		foreach ($types as $type) {
			if (in_array((int) $type, $typeFilter)) {
				$mediaTypes[] = array(
					'type' => $type,
					'label' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('type_' . $type, 'media'),
				);
			}
		}
		return $mediaTypes;
	}

}

?>