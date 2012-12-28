<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Media development team <typo3-project-media@lists.typo3.org>
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
 * Test case for class \TYPO3\CMS\Media\FormFactory\SelectFactory.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class SelectFactoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\FormFactory\SelectFactory
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $fakeName = '';

	/**
	 * @var string
	 */
	private $fakePrefix = '';

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\FormFactory\SelectFactory();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function setNecessaryValuesAndCheckWhetherMethodGetReturnAnInstanceOfSelect() {
		$fieldName = 'title';
		$actual = $this->fixture
			->setFieldName($fieldName)
			->setValue(uniqid('foo'))
			->get();

		$this->assertTrue($actual instanceof TYPO3\CMS\Media\Form\Select);
	}

	/**
	 * @test
	 * @expectedException TYPO3\CMS\Media\Exception\EmptyPropertyException
	 */
	public function ifFieldNameIsNotSetAnEmptyPropertyExceptionIsRaised() {
		$this->fixture->get();
	}

	/**
	 * @test
	 */
	public function getOptionsForFieldStorageIsGreaterThanTwo() {
		$fieldName = 'storage'; // relevant value for testing
		$this->fixture->setFieldName($fieldName);
		$actual = $this->fixture->getOptions();
		$this->assertTrue(count($actual) > 1);
	}

	/**
	 * @test
	 */
	public function getItemsFromDatabaseForFieldStorageIsNotEmpty() {
		$fieldName = 'storage'; // relevant value for testing
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($fieldName);
		$this->fixture->setFieldName($fieldName);
		$actual = $this->fixture->getItemsFromDatabase($configuration);
		$this->assertTrue(count($actual) > 0);
	}

	/**
	 * @test
	 */
	public function getItemsFromTcaForFieldTypeAndCheckWhetherItIsBiggerThanOne() {
		$fieldName = 'type'; // relevant value for testing
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($fieldName);
		$actual = $this->fixture->getItemsFromTca($configuration);
		$this->assertTrue(count($actual) > 0);
	}

	/**
	 * @test
	 */
	public function getItemsFromTcaForFieldStorageAndCheckWhetherItIsNotEmpty() {
		$fieldName = 'storage'; // relevant value for testing
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($fieldName);
		$actual = $this->fixture->getItemsFromTca($configuration);
		$this->assertTrue(count($actual) > 0);
	}

	/**
	 * @test
	 */
	public function getItemsFromTcaForFieldStatusAndCheckWhetherElementsContainsAnImageTag() {
		$fieldName = 'status'; // relevant value for testing
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($fieldName);
		$actual = $this->fixture->getItemsFromTca($configuration);
		foreach ($actual as $value) {
			$this->assertArrayHasKey('icon', $value);
			$this->assertArrayHasKey('value', $value);
		}
	}

	/**
	 * @test
	 */
	public function getOptionsForFieldStatusAndCheckWhetherElementsContainsAnImageTag() {
		$fieldName = 'status'; // relevant value for testing
		$this->fixture->setFieldName($fieldName);
		$actual = $this->fixture->getOptions();
		$this->assertTrue(count($actual) > 1);
	}

	/**
	 * @test
	 */
	public function getItemsFromTcaForFieldRankingAndCheckWhetherCellsAreInteger() {
		$fieldName = 'ranking'; // relevant value for testing
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($fieldName);
		$actual = $this->fixture->getItemsFromTca($configuration);
		foreach ($actual as $data) {
			$this->assertTrue(is_int($data['value']));
		}
	}

	/**
	 * @test
	 */
	public function foo() {
		$fieldName = 'categories'; // relevant value for testing
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($fieldName);
		print_r($configuration);
	}
}
?>