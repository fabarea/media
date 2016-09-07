<?php
namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to handle validation on the client side
 */
class ImagePresetUtility implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var array
     */
    protected $store = [];

    /**
     * @var string
     */
    protected $currentPreset = '';

    /**
     * Returns a class instance
     *
     * @return \Fab\Media\Utility\ImagePresetUtility
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance('Fab\Media\Utility\ImagePresetUtility');
    }

    /**
     * Set the current preset value. Preset values come from the settings and can be:
     * image_thumbnail, image_mini, image_small, image_medium, image_large
     *
     * @throws \Fab\Media\Exception\EmptyValueException
     * @param string $preset image_thumbnail, image_mini, ...
     * @return \Fab\Media\Utility\ImagePresetUtility
     */
    public function preset($preset)
    {
        $size = ConfigurationUtility::getInstance()->get($preset);
        if (is_null($size)) {
            throw new \Fab\Media\Exception\EmptyValueException('No value for preset: ' . $preset, 1362501066);
        }

        $this->currentPreset = $preset;
        if (!isset($this->store[$this->currentPreset])) {
            // @todo use object Dimension instead
            $dimensions = GeneralUtility::trimExplode('x', $size);
            $this->store[$this->currentPreset]['width'] = empty($dimensions[0]) ? 0 : $dimensions[0];
            $this->store[$this->currentPreset]['height'] = empty($dimensions[1]) ? 0 : $dimensions[1];
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param array $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * Returns width of the current preset.
     *
     * @throws \Fab\Media\Exception\InvalidKeyInArrayException
     * @return int
     */
    public function getWidth()
    {
        if (empty($this->store[$this->currentPreset])) {
            throw new \Fab\Media\Exception\InvalidKeyInArrayException('No existing values for current preset. Have you set a preset?', 1362501853);
        }
        return (int)$this->store[$this->currentPreset]['width'];
    }

    /**
     * Returns height of the current preset.
     *
     * @throws \Fab\Media\Exception\InvalidKeyInArrayException
     * @return int
     */
    public function getHeight()
    {
        if (empty($this->store[$this->currentPreset])) {
            throw new \Fab\Media\Exception\InvalidKeyInArrayException('No existing values for current preset. Have you set a preset?', 1362501853);
        }
        return (int)$this->store[$this->currentPreset]['height'];
    }
}
