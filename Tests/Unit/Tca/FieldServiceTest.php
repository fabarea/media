<?php
namespace TYPO3\CMS\Media\Tca;

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
 * Test case for class \TYPO3\CMS\Media\Tca\FieldService.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class FieldServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Tca\FieldService
	 */
	private $fixture;

	public function setUp() {
		$tableName = 'sys_file';
		$serviceType = 'field';
		$this->fixture = new \TYPO3\CMS\Media\Tca\FieldService($tableName, $serviceType);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function fieldsIncludesATitleFieldInTableSysFile() {
		$actual = $this->fixture->getFields();
		$this->assertTrue(is_array($actual));
		$this->assertArrayHasKey('title', $actual);
	}

	/**
	 * @test
	 */
	public function fieldTypeReturnsInputForFieldTitleInTableSysFile() {
		$actual = $this->fixture->getFieldType('title');
		$this->assertEquals('input', $actual);
	}

	/**
	 * @test
	 */
	public function fieldTypeReturnsDateForFieldModificationDateInTableSysFile() {
		$actual = $this->fixture->getFieldType('modification_date');
		$this->assertEquals('date', $actual);
	}

	/**
	 * @test
	 */
	public function fieldTypeReturnsWidgetForStringStartingWithWidget() {
		$actual = $this->fixture->getFieldType('--widget--;' . uniqid());
		$this->assertEquals('widget', $actual);
	}

	/**
	 * @test
	 */
	public function fieldTypeReturnsPaletteForStringStartingWithPalette() {
		$actual = $this->fixture->getFieldType('--palette--;' . uniqid());
		$this->assertEquals('palette', $actual);
	}

	/**
	 * @test
	 */
	public function fieldNameMustBeRequiredByDefault() {
		$this->assertTrue($this->fixture->isRequired('name'));
	}

	/**
	 * @test
	 */
	public function fieldTitleMustNotBeRequiredByDefault() {
		$this->assertFalse($this->fixture->isRequired('title'));
	}

	/**
	 * @test
	 */
	public function getLabelForFieldStatusAndItemValueOneMustReturnOK() {
		$expected = 'Ok';
		$actual = $this->fixture->getLabelForItem('status', 1);
		$this->assertSame($expected, $actual);
	}

	/**
	 * @test
	 */
	public function getIconForFieldStatusAndItemValueOneMustReturnOK() {
		$actual = $this->fixture->getIconForItem('status', 1);
		$this->assertContains('status_1', $actual);
	}

}
?>