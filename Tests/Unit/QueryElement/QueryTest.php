<?php
namespace TYPO3\CMS\Media\QueryElement;

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
 * Test case for class \TYPO3\CMS\Media\QueryElement\Query.
 */
class QueryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\QueryElement\Query
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\QueryElement\Query();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getterAndSetterForOrderPropertyAreOK() {
		$ordering = new \TYPO3\CMS\Media\QueryElement\Order();
		$this->fixture->setOrder($ordering);
		$object = $this->fixture->getOrder();
		$this->assertTrue($object instanceof \TYPO3\CMS\Media\QueryElement\Order);
	}

	/**
	 * @test
	 */
	public function renderOrderWithNoValueReturnsEmpty() {
		$ordering = new \TYPO3\CMS\Media\QueryElement\Order();
		$this->fixture->setOrder($ordering);
		$this->assertEmpty($this->fixture->renderOrder());
	}

	/**
	 * @test
	 */
	public function methodGetReturnsDefaultSql() {
		$this->assertContains('SELECT * FROM sys_file WHERE deleted = 0', $this->fixture->getQuery());
	}

	/**
	 * @test
	 */
	public function methodCountReturnAnIntegerValue() {
		$this->assertTrue(is_int($this->fixture->count()));
	}

	/**
	 * @test
	 */
	public function methodExecuteReturnAnArray() {
		$this->assertTrue(is_array($this->fixture->execute()));
	}

	/**
	 * @test
	 * @dataProvider propertyProvider
	 */
	public function testProperty($propertyName, $value) {
		$setter = 'set' . ucfirst($propertyName);
		$getter = 'get' . ucfirst($propertyName);
		$actual = call_user_func_array(array($this->fixture, $setter), array($value));
		$this->assertTrue($actual instanceof \TYPO3\CMS\Media\QueryElement\Query);
		$this->assertEquals($value, call_user_func(array($this->fixture, $getter)));
	}

	/**
	 * Provider
	 */
	public function propertyProvider() {
		return array(
			array('offset', rand(0,100)),
			array('limit', rand(0,100)),
			array('order', new \TYPO3\CMS\Media\QueryElement\Order()),
			array('matcher', new \TYPO3\CMS\Media\QueryElement\Matcher()),
			array('rawResult', uniqid()),
			array('objectType', uniqid()),
			array('ignoreEnableFields', TRUE),
		);
	}
}
?>