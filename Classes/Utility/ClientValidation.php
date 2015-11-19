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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * A class to handle validation on the client side
 */
class ClientValidation implements SingletonInterface
{

    /**
     * Returns a class instance
     *
     * @return \Fab\Media\Utility\ClientValidation
     */
    static public function getInstance()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Media\Utility\ClientValidation');
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
        if (\Fab\Media\Utility\TcaField::getService()->isRequired($fieldName)) {
            $result = ' validate[required]';
        }
        return $result;
    }
}
