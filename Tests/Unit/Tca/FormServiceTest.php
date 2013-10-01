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
 * Test case for class \TYPO3\CMS\Media\Tca\FormService.
 */
class FormServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Tca\FormService
	 */
	private $fixture;

	public function setUp() {
		$tableName = 'sys_file';
		$serviceType = 'form';
		$this->fixture = new \TYPO3\CMS\Media\Tca\FormService($tableName, $serviceType);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getTypesReturnANotEmptyArrayForTableSysFile() {
		$actual = $this->fixture->getTypes();
		$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function fieldStructureContainsTheDefaultTabAndIsBiggerThanOneByDefault() {
		$actual = $this->fixture->getFieldStructure(2);
		$this->assertArrayHasKey('LLL:EXT:cms/locallang_ttc.xml:palette.general', $actual);
		$this->assertTrue(count($actual) !== 1);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 */
	public function raiseExceptionIfTypeDoesNotExist() {
		$this->fixture->getFields(uniqid('foo'));
	}

	/**
	 * @test
	 */
	public function fieldsInFormMustBeEqualWithTypeEqualOne() {
		$this->assertEquals($this->fixture->getFields(), $this->fixture->getFields(1));
	}

}
?>