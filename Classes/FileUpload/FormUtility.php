<?php
namespace Fab\Media\FileUpload;

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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class that optimize an image according to some settings.
 */
class FormUtility implements SingletonInterface
{

    /**
     * Returns a class instance.
     *
     * @return \Fab\Media\FileUpload\FormUtility
     */
    static public function getInstance()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Media\FileUpload\FormUtility');
    }

    /**
     * Tells whether the content type is valid.
     *
     * @return bool
     */
    public function hasValidContentType()
    {
        return isset($GLOBALS['_SERVER']['CONTENT_TYPE']);
    }

    /**
     * Tells whether the form is multiparted, e.g "multipart/form-data"
     *
     * @return bool
     */
    public function isMultiparted()
    {
        return strpos(strtolower($GLOBALS['_SERVER']['CONTENT_TYPE']), 'multipart/form-data') === 0;
    }

    /**
     * Tells whether the form is URL encoded, e.g "application/x-www-form-urlencoded; charset=UTF-8"
     *
     * @return bool
     */
    public function isUrlEncoded()
    {
        return strpos(strtolower($GLOBALS['_SERVER']['CONTENT_TYPE']), 'application/x-www-form-urlencoded') === 0;
    }

    /**
     * Tells whether the form is octet streamed, e.g "application/x-www-form-urlencoded; charset=UTF-8"
     *
     * @return bool
     */
    public function isOctetStreamed()
    {
        return strpos(strtolower($GLOBALS['_SERVER']['CONTENT_TYPE']), 'application/octet-stream') === 0;
    }

}
