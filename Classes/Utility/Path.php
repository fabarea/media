<?php
namespace Fab\Media\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * A class to handle public resource path
 */
class Path
{

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
    static public function getRelativePath($resource)
    {

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
    static public function resolvePath($resource)
    {
        $resource = self::canonicalPath($resource);
        if (!is_file(PATH_site . $resource)) {
            $resource = 'EXT:' . GeneralUtility::camelCaseToLowerCaseUnderscored(self::$extensionName) . '/Resources/Public/' . $resource;
        }
        return GeneralUtility::getFileAbsFileName($resource);
    }

    /**
     * Tell whether a resource exist.
     *
     * @param string $resource
     * @return string
     */
    static public function exists($resource)
    {
        return is_file(self::resolvePath($resource));
    }

    /**
     * Tell whether a resource does not exist.
     *
     * @param string $resource
     * @return string
     */
    static public function notExists($resource)
    {
        return !self::exists($resource);
    }

    /**
     * Returns a canonical path by stripping relative segment ../foo/../bar will become foo/bar
     *
     * @param $resource
     * @return string
     */
    static public function canonicalPath($resource)
    {
        $segments = explode('/', $resource);
        $keys = array_keys($segments, '..');
        foreach ($keys as $key) {
            unset($segments[$key]);
        }
        return implode('/', $segments);
    }
}
