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
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * A class for handling storage
 */
class StorageUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var ResourceStorage
	 */
	protected $currentStorage;

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\Utility\StorageUtility
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\StorageUtility');
	}

	/**
	 * Returns the current file storage in use.
	 *
	 * @return ResourceStorage
	 */
	public function getCurrentStorage() {
		if (is_null($this->currentStorage)) {

			// Get the parameter prefix for checking if a particular storage is requested.
			$parameterPrefix = $this->getModuleLoader()->getParameterPrefix();
			$parameters = GeneralUtility::_GET($parameterPrefix);

			// Default value
			$identifierParameter = NULL;

			// Get last selected storage from User settings
			if (SessionUtility::getInstance()->get('lastSelectedStorage') > 0) {
				$identifierParameter = SessionUtility::getInstance()->get('lastSelectedStorage');
			}

			// Override selected storage if get parameter is seen.
			if (!empty($parameters['storage']) && (int) $parameters['storage'] > 0) {
				$identifierParameter = (int) $parameters['storage'];

				// Save state
				SessionUtility::getInstance()->set('lastSelectedStorage', $identifierParameter);
			}

			$this->currentStorage = ObjectFactory::getInstance()->getStorage($identifierParameter);
		}
		return $this->currentStorage;
	}

	/**
	 * Return the module loader.
	 *
	 * @return \TYPO3\CMS\Vidi\ModuleLoader
	 */
	public function getModuleLoader() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\ModuleLoader');
	}

}
?>
