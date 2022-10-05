<?php

namespace Fab\Media\Module;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for retrieving information about the Media module.
 */
class VidiModule
{
    public const SIGNATURE = 'file_VidiSysFileM1';
    public const PARAMETER_PREFIX = 'tx_vidi_file_vidisysfilem1';

    public const SIGNATURE_FOLDER_TREE_OMITTED = 'content_VidiSysFileM1';
    public const PARAMETER_PREFIX_FOLDER_TREE_OMITTED = 'tx_vidi_content_vidisysfilem1';

    /**
     * @return string
     */
    public static function getSignature()
    {
        return self::getMediaModule()->hasFolderTree() ? self::SIGNATURE : self::SIGNATURE_FOLDER_TREE_OMITTED;
    }

    /**
     * @return string
     */
    public static function getParameterPrefix()
    {
        return self::getMediaModule()->hasFolderTree() ? self::PARAMETER_PREFIX : self::PARAMETER_PREFIX_FOLDER_TREE_OMITTED;
    }

    /**
     * @return MediaModule|object
     */
    protected static function getMediaModule()
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }
}
