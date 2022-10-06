<?php

namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * A class to handle validation on the client side
 */
class ClientValidation implements SingletonInterface
{
    /**
     * Returns a class instance
     *
     * @return \Fab\Media\Utility\ClientValidation|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(\Fab\Media\Utility\ClientValidation::class);
    }

    /**
     * Get the validation class name given a field.
     *
     * @param string $fieldName
     * @return string
     */
    public function get($fieldName)
    {
        $result = '';
        if (TcaField::getService()->isRequired($fieldName)) {
            $result = ' validate[required]';
        }
        return $result;
    }
}
