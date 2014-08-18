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
	 * @return \TYPO3\CMS\Vidi\Module\ModuleLoader
	 */
	public function getModuleLoader() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Module\ModuleLoader');
	}

}
