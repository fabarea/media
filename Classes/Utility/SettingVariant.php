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
 * A class for handling variants settings
 */
class SettingVariant implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $variations = array();

	/**
	 * @var \TYPO3\CMS\Media\Utility\Setting
	 */
	protected $setting;

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\SettingVariant
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\SettingVariant');
	}

	/**
	 * Constructor
	 *
	 * @return \TYPO3\CMS\Media\Utility\SettingVariant
	 */
	public function __construct() {
		$this->setting = \TYPO3\CMS\Media\Utility\Setting::getInstance();

		if ($this->setting->get('variations')) {
			$variations = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->setting->get('variations'));

			foreach ($variations as $variation) {
				$dimensions = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('x', $variation);
				$_variation['width'] = empty($dimensions[0]) ? 0 : $dimensions[0];
				$_variation['height'] = empty($dimensions[1]) ? 0 : $dimensions[1];
				$this->variations[] = $_variation;
			}
		}
	}

	/**
	 * @return array
	 */
	public function getVariations() {
		return $this->variations;
	}

	/**
	 * @param array $variations
	 */
	public function setVariations($variations) {
		$this->variations = $variations;
	}
}
?>
