<?php
namespace TYPO3\CMS\Media\Form;

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
 * A class for handling configuration
 */
class Configuration {

	/**
	 * @var array
	 */
	static protected $configuration = array(
		'width' => 'span8',
	);

	/**
	 * @var string
	 */
	static protected $extensionKey = 'media';

	/**
	 * Returns a configuration key
	 *
	 * @param string $key
	 * @return array
	 */
	static public function get($key) {
		return isset(self::$configuration[$key]) ? self::$configuration[$key] : '';
	}

	/**
	 * @return array
	 */
	public static function getConfiguration() {
		return self::$configuration;
	}

	/**
	 * @param array $configuration
	 */
	public static function setConfiguration($configuration) {
		self::$configuration = $configuration;
	}

	/**
	 * @param string $key
	 * @param mixed $configuration
	 */
	public static function addConfiguration($key, $configuration) {
		self::$configuration[$key] = $configuration;
	}

}
?>