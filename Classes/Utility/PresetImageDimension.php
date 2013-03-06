<?php
namespace TYPO3\CMS\Media\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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

/**
 * A class to handle validation on the client side
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class PresetImageDimension implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $store = array();

	/**
	 * @var string
	 */
	protected $currentPreset = '';

	/**
	 * Returns a class instance
	 *
	 * @return \TYPO3\CMS\Media\Utility\PresetImageDimension
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\PresetImageDimension');
	}

	/**
	 * Set the current preset value
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyValueException
	 * @param string $preset
	 * @return \TYPO3\CMS\Media\Utility\PresetImageDimension
	 */
	public function preset($preset){
		$size = \TYPO3\CMS\Media\Utility\Configuration::get($preset);
		if (!$size) {
			throw new \TYPO3\CMS\Media\Exception\EmptyValueException('No value for preset: ' . $preset, 1362501066);
		}

		$this->currentPreset = $preset;
		if (!isset($this->store[$this->currentPreset])) {
			$dimensions = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('x', $size);
			$this->store[$this->currentPreset]['width'] = $dimensions[0];
			$this->store[$this->currentPreset]['height'] = $dimensions[1];
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getStore() {
		return $this->store;
	}

	/**
	 * @param array $store
	 */
	public function setStore($store) {
		$this->store = $store;
	}

	/**
	 * Returns width of the current preset.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @return int
	 */
	public function getWidth(){
		if (empty($this->store[$this->currentPreset])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('No existing values for current preset. Have you set a preset?', 1362501853);
		}
		return (int) $this->store[$this->currentPreset]['width'];
	}

	/**
	 * Returns height of the current preset.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @return int
	 */
	public function getHeight() {
		if (empty($this->store[$this->currentPreset])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('No existing values for current preset. Have you set a preset?', 1362501853);
		}
		return (int) $this->store[$this->currentPreset]['height'];
	}
}
?>