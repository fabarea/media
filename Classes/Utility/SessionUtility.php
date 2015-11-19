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

/**
 * A class for handling the User Session
 */
class SessionUtility implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string
     */
    protected $moduleKey = 'media';

    /**
     * Returns a class instance.
     *
     * @return \Fab\Media\Utility\SessionUtility
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance('Fab\Media\Utility\SessionUtility');
    }

    public function __construct()
    {

        // Initialize storage from the current
        if (!is_array($this->getBackendUser()->uc['moduleData'][$this->moduleKey])) {
            $this->getBackendUser()->uc['moduleData'][$this->moduleKey] = array();
            $this->getBackendUser()->writeUC();
        }
    }

    /**
     * Return a value from the Session according to the key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->getBackendUser()->uc['moduleData'][$this->moduleKey][$key];
    }

    /**
     * Set a key to the Session.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->getBackendUser()->uc['moduleData'][$this->moduleKey][$key] = $value;
        $this->getBackendUser()->writeUC();
    }

    /**
     * Returns an instance of the current Backend User.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
