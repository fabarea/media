<?php
namespace TYPO3\CMS\Media\Tca;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \TYPO3\CMS\Media\Tca\ServiceFactory.
 */
class ServiceFactoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Tca\ServiceFactory
	 */
	private $fixture;

	public function setUp() {
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function instantiateVariousFieldServicesForTableSysFile() {
		$tableName = 'sys_file';
		foreach (array('field', 'table', 'grid', 'form') as $serviceType) {
			$fieldService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);
			$instanceName = sprintf('\TYPO3\CMS\Media\Tca\%sService', $serviceType);
			$this->assertTrue($fieldService instanceof $instanceName);
		}
	}

	/**
	 * @test
	 */
	public function instantiateVariousFieldServicesAndCheckWhetherTheClassInstanceIsStored() {
		$tableName = 'sys_file';
		foreach (array('field', 'table', 'grid') as $serviceType) {
			\TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);
			$instanceName = sprintf('\TYPO3\CMS\Media\Tca\%sService', $serviceType);
			$storage = \TYPO3\CMS\Media\Tca\ServiceFactory::getInstanceStorage();
			$this->assertTrue($storage[$tableName][$serviceType] instanceof $instanceName);
		}
	}

	/**
	 * @test
	 */
	public function instantiateTableServicesForTableSysFile() {
		$tableName = 'sys_file';
		$serviceType = 'table';
		$fieldService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);
		$instanceName = sprintf('\TYPO3\CMS\Media\Tca\%sService', $serviceType);
		$this->assertTrue($fieldService instanceof $instanceName);
	}

	/**
	 * @test
	 */
	public function instantiateGridServicesForTableSysFile() {
		$tableName = 'sys_file';
		$serviceType = 'grid';
		$fieldService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);
		$instanceName = sprintf('\TYPO3\CMS\Media\Tca\%sService', $serviceType);
		$this->assertTrue($fieldService instanceof $instanceName);
	}

	/**
	 * @test
	 */
	public function instantiateFieldServicesForTableSysFile() {
		$tableName = 'sys_file';
		$serviceType = 'field';
		$fieldService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);
		$instanceName = sprintf('\TYPO3\CMS\Media\Tca\%sService', $serviceType);
		$this->assertTrue($fieldService instanceof $instanceName);
	}

	/**
	 * @test
	 */
	public function instantiateFormServicesForTableSysFile() {
		$tableName = 'sys_file';
		$serviceType = 'form';
		$fieldService = \TYPO3\CMS\Media\Tca\ServiceFactory::getService($tableName, $serviceType);
		$instanceName = sprintf('\TYPO3\CMS\Media\Tca\%sService', $serviceType);
		$this->assertTrue($fieldService instanceof $instanceName);
	}

}
?>