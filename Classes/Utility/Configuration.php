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
 * A class for handling general settings
 */
class Configuration {

	/**
	 * @var array
	 */
	static protected $settings = array();

	/**
	 * @var array
	 */
	static protected $defaultSettings = array(
		'thumbnail_size' => 100,
		'storage' => 1,
		'recycler_folder' => '_recycler_',
		'mount_point_for_file_type_1' => 0, // text (txt, html, ...)
		'mount_point_for_file_type_2' => 0, // image
		'mount_point_for_file_type_3' => 0, // audio
		'mount_point_for_file_type_4' => 0, // video
		'mount_point_for_file_type_5' => 0, // application (pdf, doc, ...)
	);

	/**
	 * @var string
	 */
	static protected $extensionKey = 'media';

	/**
	 * Returns a configuration key.
	 *
	 * @param string $key
	 * @return array
	 */
	static public function get($key) {
		$settings = self::getSettings();
		return isset($settings[$key]) ? $settings[$key] : '';
	}

	/**
	 * Set a configuration key.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	static public function set($key, $value) {
		self::getSettings(); // just makes sure the settings have been instantiate once.
		self::$settings[$key] = $value;
	}

	/**
	 * @return array
	 */
	public static function getSettings() {
		if (empty(self::$settings)) {
			$settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::$extensionKey]);
			self::$settings = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge(self::$defaultSettings, $settings);
		}
		return self::$settings;
	}

	/**
	 * @param array $settings
	 */
	public static function setSettings($settings) {
		self::$settings = $settings;
	}
}
?>
