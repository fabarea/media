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
 * Test case for class \TYPO3\CMS\Media\Utility\Grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class GridTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Utility\Grid
	 */
	protected $fixture;

	public function setUp() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Utility\Grid();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getListOfColumnsReturnsNotEmpty() {
		$actual = $this->fixture->getListOfColumns();

		$this->assertTrue(is_array($actual));
		$this->assertNotEmpty($actual);
		$this->assertTrue(in_array('title', $actual));
	}

	/**
	 * @test
	 */
	public function getColumnsReturnsNotEmpty() {
		$actual = $this->fixture->getColumns();
		$this->assertTrue(is_array($actual));
		$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function getConfigurationForColumnTitle() {
		$actual = $this->fixture->getColumn('title');
		$this->assertTrue(is_array($actual));
		$this->assertTrue(count($actual) > 0);
	}

	/**
	 * @test
	 */
	public function columnTitleIsNotInternal() {
		$this->assertFalse($this->fixture->isInternal('title'));
	}

	/**
	 * @test
	 */
	public function columnNumberIsInternal() {
		$this->assertTrue($this->fixture->isInternal('_number'));
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
	public function labelOfColumnTstampShouldReturnsValueUpdated() {
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
		$this->assertFalse($this->fixture->isSortable('_buttons'));
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
		$this->assertTrue($this->fixture->hasRenderer('name'));
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
		$expected = 'TYPO3\CMS\Media\Renderer\Preview';
		$this->assertEquals($expected, $this->fixture->getRenderer('name'));
	}

	/**
	 * @test
	 */
	public function getTheRendererOfColumnFoo() {
		$expected = '';
		$this->assertEquals($expected, $this->fixture->getRenderer(uniqid('foo')));
	}

}
?>