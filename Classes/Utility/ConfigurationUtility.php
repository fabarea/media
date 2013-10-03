<?php
namespace TYPO3\CMS\Media\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * A class for handling configuration of the extension
 */
class ConfigurationUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var string
	 */
	protected $extensionKey = 'media';

	/**
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\ConfigurationUtility
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\ConfigurationUtility');
	}

	/**
	 * Constructor
	 *
	 * @return \TYPO3\CMS\Media\Utility\ConfigurationUtility
	 */
	public function __construct() {

		/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
		$configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
		$configuration = $configurationUtility->getCurrentConfiguration($this->extensionKey);

		// Fill up configuration array with relevant values.
		foreach ($configuration as $key => $data) {
			$this->configuration[$key] = $data['value'];
		}
	}

	/**
	 * Returns a setting key.
	 *
	 * @param string $key
	 * @return array
	 */
	public function get($key) {
		return isset($this->configuration[$key]) ? trim($this->configuration[$key]) : NULL;
	}

	/**
	 * Set a setting key.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value) {
		$this->configuration[$key] = $value;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}
}
?>
