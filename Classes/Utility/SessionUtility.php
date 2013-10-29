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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class for handling the User Session
 */
class SessionUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var string
	 */
	protected $moduleKey = 'media';

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\SessionUtility
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\SessionUtility');
	}

	public function __construct() {

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
	public function get($key){
		return $this->getBackendUser()->uc['moduleData'][$this->moduleKey][$key];
	}

	/**
	 * Set a key to the Session.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value) {
		$this->getBackendUser()->uc['moduleData'][$this->moduleKey][$key] = $value;
		$this->getBackendUser()->writeUC();
	}

	/**
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
	}
}
?>
