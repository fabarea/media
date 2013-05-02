<?php
namespace TYPO3\CMS\Media\Utility;

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
 * Test case for class \TYPO3\CMS\Media\Utility\SettingPermission.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class SettingPermissionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Utility\SettingPermission
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Utility\SettingPermission();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function propertyPermissionIsAnArrayNotEmpty() {
		$this->assertAttributeNotEmpty('permissions', $this->fixture);
	}

	/**
	 * @test
	 */
	public function returnedTypeIsByDefaultArray() {
		$expected = 'array';
		$this->assertSame($expected, $this->fixture->getReturnedType());
	}

	/**
	 * @test
	 */
	public function getListOfAllowedExtensionReturnsAnArrayNotEmptyByDefault() {
		$actual = $this->fixture->getAllowedExtensions();
		$this->assertInternalType('array', $actual);
		$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function getListOfAllowedExtensionAndSetReturnedTypeStringReturnsStringNotEmpty() {
		$actual = $this->fixture->returnString()->getAllowedExtensions();
		$this->assertInternalType('string', $actual);
		$this->assertNotEmpty($actual);
	}
}
?>