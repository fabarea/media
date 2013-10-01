<?php
namespace TYPO3\CMS\Media\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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

use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * A class to handle public resource path
 */
class Path {

	/**
	 * @var string
	 */
	static protected $extensionName = 'media';

	/**
	 * Return a public path pointing to a resource.
	 *
	 * @param string $resource
	 * @return string
	 */
	static public function getRelativePath($resource) {

		// If file is not found, resolve the path
		if (!is_file(PATH_site . $resource)) {
			$resource = substr(self::resolvePath($resource), strlen(PATH_site));
		}

		return PathUtility::getRelativePathTo(PathUtility::dirname(PATH_site . $resource)) . PathUtility::basename($resource);
	}

	/**
	 * Resolves path e.g. EXT:media/Resources/Public/foo.png or ../../foo and returns an absolute path to the given resource.
	 *
	 * @param string $resource
	 * @return string
	 */
	static public function resolvePath($resource) {
		$resource = self::canonicalPath($resource);
		if (!is_file(PATH_site . $resource)) {
			$resource = 'EXT:' . \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored(self::$extensionName) . '/Resources/Public/' . $resource;
		}
		return \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($resource);
	}

	/**
	 * Tell whether a resource exist.
	 *
	 * @param string $resource
	 * @return string
	 */
	static public function exists($resource) {
		return is_file(self::resolvePath($resource));
	}

	/**
	 * Tell whether a resource does not exist.
	 *
	 * @param string $resource
	 * @return string
	 */
	static public function notExists($resource) {
		return !self::exists($resource);
	}

	/**
	 * Returns a canonical path by stripping relative segment ../foo/../bar will become foo/bar
	 *
	 * @param $resource
	 * @return string
	 */
	static public function canonicalPath($resource) {
		$segments = explode('/', $resource);
		$keys = array_keys($segments, '..');
		foreach ($keys as $key) {
			unset($segments[$key]);
		}
		return implode('/', $segments);
	}
}
?>