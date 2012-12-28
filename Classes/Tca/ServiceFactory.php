<?php
namespace TYPO3\CMS\Media\Tca;

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
 * A class to handle TCA ctrl.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class ServiceFactory implements \TYPO3\CMS\Core\SingletonInterface {

	const TABLE = 'table';
	const FIELD = 'field';
	const GRID = 'grid';
	const FORM = 'form';

	/**
	 * @var array
	 */
	static protected $instanceStorage;

	/**
	 * Returns a class instance of a corresponding TCA service.
	 * If the class instance does not exist, create one.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\NotExistingClassException
	 * @param string $tableName
	 * @param string $serviceType of the TCA. Typical values are: field, table, grid
	 * @return \TYPO3\CMS\Media\Tca\ServiceInterface
	 */
	static public function getService($tableName, $serviceType) {
		if (empty(self::$instanceStorage[$tableName][$serviceType])) {
			$className = sprintf('TYPO3\CMS\Media\Tca\%sService', ucfirst($serviceType));

			if (! class_exists($className)) {
				throw new \TYPO3\CMS\Media\Exception\NotExistingClassException('Class does not exit: ' . $className, 1357060937);

			}
			$instance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className, $tableName, $serviceType);
			self::$instanceStorage[$tableName][$serviceType] = $instance;
		}
		return self::$instanceStorage[$tableName][$serviceType];
	}

	/**
	 * Returns a class instance of a corresponding TCA service.
	 * This is a shorthand method for "field" (AKA "columns").
	 *
	 * @param string $tableName
	 * @return \TYPO3\CMS\Media\Tca\FieldService
	 */
	static public function getFieldService($tableName) {
		return \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, \TYPO3\CMS\Media\Tca\ServiceFactory::FIELD);
	}

	/**
	 * Returns a class instance of a corresponding TCA service.
	 * This is a shorthand method for "grid".
	 *
	 * @param string $tableName
	 * @return \TYPO3\CMS\Media\Tca\GridService
	 */
	static public function getGridService($tableName) {
		return \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, \TYPO3\CMS\Media\Tca\ServiceFactory::GRID);
	}

	/**
	 * Returns a class instance of a corresponding TCA service.
	 * This is a shorthand method for "table" (AKA "ctrl").
	 *
	 * @param string $tableName
	 * @return \TYPO3\CMS\Media\Tca\TableService
	 */
	static public function getTableService($tableName) {
		return \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, \TYPO3\CMS\Media\Tca\ServiceFactory::TABLE);
	}

	/**
	 * Returns a class instance of a corresponding TCA service.
	 * This is a shorthand method for "form" (AKA "types").
	 *
	 * @param string $tableName
	 * @return \TYPO3\CMS\Media\Tca\FormService
	 */
	static public function getFormService($tableName) {
		return \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, \TYPO3\CMS\Media\Tca\ServiceFactory::FORM);
	}

	/**
	 * @return array
	 */
	public static function getInstanceStorage() {
		return self::$instanceStorage;
	}
}
?>