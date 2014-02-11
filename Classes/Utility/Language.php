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
 * A class for handling language
 */
class Language implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $languages;

	/**
	 * @var array
	 */
	protected $languageUids;

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\Language
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\Language');
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Returns available language
	 *
	 * @return array
	 */
	public function getLanguages() {
		if (is_null($this->languages)) {
			// @todo make an utility language singletong
			$this->languages = $this->databaseHandler->exec_SELECTgetRows('*', 'sys_language', 'hidden = 0');
		}
		return $this->languages;
	}

	/**
	 * Returns available language
	 *
	 * @return array
	 */
	public function getLanguageUids() {
		if (is_null($this->languageUids)) {
			foreach ($this->getLanguages() as $language) {
				$this->languageUids[] = $language['uid'];
			}
		}
		return $this->languageUids;
	}
}
