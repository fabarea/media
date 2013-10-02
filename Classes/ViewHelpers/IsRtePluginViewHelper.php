<?php
namespace TYPO3\CMS\Media\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * View helper which tells whether a RTE plugin is requested from the URL parameters
 */
class IsRtePluginViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Tells whether the given RTE plugin corresponds to a parameter.
	 *
	 * @param string $plugin
	 * @return boolean
	 */
	public function render($plugin) {
		$parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_media_user_mediam1');
		return $parameters['rtePlugin'] == $plugin || $parameters['action'] == $plugin;
	}
}

?>