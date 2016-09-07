<?php
namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * A class for handling configuration of the extension
 */
class ConfigurationUtility implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string
     */
    protected $extensionKey = 'media';

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * Returns a class instance.
     *
     * @return \Fab\Media\Utility\ConfigurationUtility
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance('Fab\Media\Utility\ConfigurationUtility');
    }

    /**
     * Constructor
     *
     * @return \Fab\Media\Utility\ConfigurationUtility
     */
    public function __construct()
    {

        /** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->getObjectManager()->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
        $configuration = $configurationUtility->getCurrentConfiguration($this->extensionKey);

        // Fill up configuration array with relevant values.
        foreach ($configuration as $key => $data) {
            $this->configuration[$key] = $data['value'];
        }
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
    }

    /**
     * Returns a setting key.
     *
     * @param string $key
     * @return array
     */
    public function get($key)
    {
        return isset($this->configuration[$key]) ? trim($this->configuration[$key]) : null;
    }

    /**
     * Set a setting key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->configuration[$key] = $value;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
