<?php
namespace TYPO3\CMS\Media\ViewHelpers;

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
 * View helper a transaction number
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class TransactionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Return the number of the transaction with the client
	 *
	 * @return string the value
	 * @api
	 */
	public function render() {
		$transaction = 0;
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sEcho')) {
			$transaction = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('sEcho') + 1;
		}
		return $transaction;
	}
}
?>