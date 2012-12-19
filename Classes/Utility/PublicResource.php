<?php
namespace TYPO3\CMS\Media\Utility;

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
 * A class to handle public resource path
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class PublicResource {

	/**
	 * @var string
	 */
	static protected $extensionName = 'media';

	/**
	 * Return a public path to a resource
	 *
	 * @param string $resource
	 * @return string
	 */
	static public function getPublicPath($resource) {
		return substr(self::getAbsolutePath($resource), strlen(PATH_site));
	}

	/**
	 * Return an absolute path to a resource
	 *
	 * @param string $resource
	 * @return string
	 */
	static public function getAbsolutePath($resource) {
		$uri = 'EXT:' . \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored(self::$extensionName) . '/Resources/Public/' . $resource;
		return \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($uri);
	}
}
?>