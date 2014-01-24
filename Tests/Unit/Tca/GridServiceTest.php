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
 * Test case for class \TYPO3\CMS\Media\Tca\GridService.
 */
class GridServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Tca\GridService
	 */
	private $fixture;

	public function setUp() {
		$tableName = 'sys_file';
		$serviceType = 'grid';
		$this->fixture = new \TYPO3\CMS\Media\Tca\GridService($tableName, $serviceType);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getLabelReturnNameAsValue() {
		$this->assertEquals('Filename', $this->fixture->getLabel('name'));
	}

	/**
	 * @test
	 */
	public function getFieldListReturnsNotEmpty() {
		$actual = $this->fixture->getFieldList();

		$this->assertTrue(is_array($actual));
		$this->assertNotEmpty($actual);
		$this->assertTrue(in_array('title', $actual));
	}

	/**
	 * @test
	 */
	public function getColumnsReturnsNotEmpty() {
		$actual = $this->fixture->getFields();
		$this->assertTrue(is_array($actual));
		$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function getConfigurationForColumnTitle() {
		$actual = $this->fixture->getField('title');
		$this->assertTrue(is_array($actual));
		$this->assertTrue(count($actual) > 0);
	}

	/**
	 * @test
	 */
	public function columnTitleIsNotInternal() {
		$this->assertFalse($this->fixture->isSystem('title'));
	}

	/**
	 * @test
	 */
	public function columnNumberIsInternal() {
		$this->assertTrue($this->fixture->isSystem('__number'));
	}

	/**
	 * @test
	 */
	public function labelOfColumnTitleShouldBeTitleByDefault() {
		$this->assertEquals('Title', $this->fixture->getLabel('title'));
	}

	/**
	 * @test
	 */
	public function labelOfColumnTstampShouldReturnsUpdatedAsValue() {
		$this->assertEquals('Updated', $this->fixture->getLabel('tstamp'));
	}

	/**
	 * @test
	 */
	public function labelOfColumnTstampHasALabel() {
		$this->assertTrue($this->fixture->hasLabel('tstamp'));
	}

	/**
	 * @test
	 */
	public function labelOfColumnFooShouldBeEmpty() {
		$this->assertEmpty($this->fixture->getLabel(uniqid('foo_')));
	}

	/**
	 * @test
	 */
	public function columnTitleShouldBeSortableByDefault() {
		$this->assertTrue($this->fixture->isSortable('title'));
	}

	/**
	 * @test
	 */
	public function columnNumberShouldBeNotSortableByDefault() {
		$this->assertFalse($this->fixture->isSortable('__buttons'));
	}

	/**
	 * @test
	 */
	public function columnTitleShouldBeVisibleByDefault() {
		$this->assertTrue($this->fixture->isVisible('title'));
	}

	/**
	 * @test
	 */
	public function columnTstampShouldBeNotVisibleByDefault() {
		$this->assertFalse($this->fixture->isVisible('tstamp'));
	}

	/**
	 * @test
	 */
	public function columnNameHasARenderer() {
		$this->assertTrue($this->fixture->hasRenderer('fileinfo'));
	}

	/**
	 * @test
	 */
	public function columnFooHasNoRenderer() {
		$this->assertFalse($this->fixture->hasRenderer(uniqid('foo')));
	}

	/**
	 * @test
	 */
	public function getTheRendererOfColumnName() {
		$expected = 'TYPO3\CMS\Media\Grid\PreviewRenderer';
		$this->assertEquals($expected, $this->fixture->getRenderer('fileinfo'));
	}

	/**
	 * @test
	 */
	public function getTheRendererOfColumnFoo() {
		$expected = '';
		$this->assertEquals($expected, $this->fixture->getRenderer(uniqid('foo')));
	}

	/**
	 * @test
	 */
	public function getFieldsAndCheckWhetherItsPositionReturnsTheCorrectFieldName() {
		$fields = array_keys($this->fixture->getFields());
		for ($index = 0; $index < count($fields); $index++) {
			$actual = $this->fixture->getFieldNameByPosition($index);
			$this->assertSame($fields[$index], $actual);
		}
	}

}
?>