<?php
namespace TYPO3\CMS\Media\FileUpload;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Class that optimize an image according to some settings.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class FormUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\FormUtility
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\FormUtility');
	}

	/**
	 * Tells whether the content type is valid.
	 *
	 * @return bool
	 */
	public function hasValidContentType() {
		return isset($GLOBALS['_SERVER']['CONTENT_TYPE']);
	}

	/**
	 * Tells whether the form is multiparted, e.g "multipart/form-data"
	 *
	 * @return bool
	 */
	public function isMultiparted() {
		return strpos(strtolower($GLOBALS['_SERVER']['CONTENT_TYPE']), 'multipart/form-data') === 0;
	}

	/**
	 * Tells whether the form is URL encoded, e.g "application/x-www-form-urlencoded; charset=UTF-8"
	 *
	 * @return bool
	 */
	public function isUrlEncoded() {
		return strpos(strtolower($GLOBALS['_SERVER']['CONTENT_TYPE']), 'application/x-www-form-urlencoded') === 0;
	}

	/**
	 * Tells whether the form is octet streamed, e.g "application/x-www-form-urlencoded; charset=UTF-8"
	 *
	 * @return bool
	 */
	public function isOctetStreamed() {
		return strpos(strtolower($GLOBALS['_SERVER']['CONTENT_TYPE']), 'application/octet-stream') === 0;
	}

}
?>