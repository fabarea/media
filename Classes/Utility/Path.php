<?php

namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Core\Environment;
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
    protected static $extensionName = 'media';

    /**
     * Return a public path pointing to a resource.
     *
     * @param string $resource
     * @return string
     */
    public static function getRelativePath($resource)
    {
        // If file is not found, resolve the path
        if (!is_file(Environment::getPublicPath() . '/' . $resource)) {
            $resource = substr(self::resolvePath($resource), strlen(Environment::getPublicPath() . '/'));
        }

        return PathUtility::getRelativePathTo(PathUtility::dirname(Environment::getPublicPath() . '/' . $resource)) . PathUtility::basename($resource);
    }

    /**
     * Resolves path e.g. EXT:media/Resources/Public/foo.png or ../../foo and returns an absolute path to the given resource.
     *
     * @param string $resource
     * @return string
     */
    public static function resolvePath($resource)
    {
        $resource = self::canonicalPath($resource);
        if (!is_file(Environment::getPublicPath() . '/' . $resource)) {
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
    public static function exists($resource)
    {
        return is_file(self::resolvePath($resource));
    }

    /**
     * Tell whether a resource does not exist.
     *
     * @param string $resource
     * @return string
     */
    public static function notExists($resource)
    {
        return !self::exists($resource);
    }

    /**
     * Returns a canonical path by stripping relative segment ../foo/../bar will become foo/bar
     *
     * @param $resource
     * @return string
     */
    public static function canonicalPath($resource)
    {
        $segments = explode('/', $resource);
        $keys = array_keys($segments, '..');
        foreach ($keys as $key) {
            unset($segments[$key]);
        }
        return implode('/', $segments);
    }
}
