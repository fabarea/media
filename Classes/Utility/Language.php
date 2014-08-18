<?php
namespace TYPO3\CMS\Media\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
