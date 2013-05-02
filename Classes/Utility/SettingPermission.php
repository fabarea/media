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
 * A class for handling permission
 */
class SettingPermission implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Media\Utility\Setting
	 */
	protected $setting;

	/**
	 * @var array
	 */
	protected $permissions;

	/**
	 * @var array
	 */
	protected $returnedType = 'array';

	/**
	 * @var array
	 */
	protected $fileTypes = array(
		\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT,
		\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE,
		\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO,
		\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO,
		\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION,
	);

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\SettingPermission
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\SettingPermission');
	}

	/**
	 * Constructor
	 *
	 * @return \TYPO3\CMS\Media\Utility\SettingPermission
	 */
	public function __construct() {
		$this->setting = \TYPO3\CMS\Media\Utility\Setting::getInstance();

		// Fill permissions tables
		foreach ($this->fileTypes as $type) {
			$_permission = $this->setting->get('extension_allowed_file_type_' . $type);
			$this->permissions[$type] = \TYPO3\CMS\Core\Utility\GeneralUtility::expandList($_permission);
		}
	}

	/**
	 * Returns list of allowed extension.
	 *
	 * @return array|string
	 */
	public function getAllowedExtensions() {

		$result = array();
		foreach ($this->permissions as $permission) {
			$result = array_merge($result, \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $permission));
		}

		if ($this->returnedType == 'string') {
			$result = implode(',', $result);
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function getFileTypes() {
		return $this->fileTypes;
	}

	/**
	 * @param array $fileTypes
	 */
	public function setFileTypes($fileTypes) {
		$this->fileTypes = $fileTypes;
	}

	/**
	 * @return \TYPO3\CMS\Media\Utility\SettingPermission
	 */
	public function returnArray() {
		$this->returnedType = 'array';
		return $this;
	}

	/**
	 * @return \TYPO3\CMS\Media\Utility\SettingPermission
	 */
	public function returnString() {
		$this->returnedType = 'string';
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReturnedType() {
		return $this->returnedType;
	}
}
?>
