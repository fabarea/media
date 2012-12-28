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
 * Test case for class \TYPO3\CMS\Media\Utility\MediaType.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaTypeTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var array
	 */
	private $values;

	public function setUp() {
		$this->values = array(
			0 => 'unknown',
			1 => 'text',
			2 => 'image',
			3 => 'audio',
			4 => 'video',
			5 => 'software'
		);
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function mediaTypeReturnsUnknownForMimeTypeRandom() {
		$expected = 'unknown';
		$actual = \TYPO3\CMS\Media\Utility\MediaType::toName(rand(10000, 100000));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @test
	 */
	public function mediaTypeReturnsZeroForMimeTypeRandom() {
		$expected = 0;
		$actual = \TYPO3\CMS\Media\Utility\MediaType::toInteger(uniqid('foo'));
		$this->assertSame($expected, $actual);
	}

	/**
	 * @test
	 */
	public function returnsItSelfWhenIntegerIsPassedToMethodToInteger() {
		$expected = rand(10000, 100000);
		$actual = \TYPO3\CMS\Media\Utility\MediaType::toInteger($expected);
		$this->assertSame($expected, $actual);
	}

	/**
	 * @test
	 */
	public function returnsImageWhenTwoAsNumericalStringIsGiven() {
		$expected = 'image';
		$actual = \TYPO3\CMS\Media\Utility\MediaType::toName('2');
		$this->assertSame($expected, $actual);
	}

	/**
	 * @test
	 */
	public function returnsItSelfWhenStringIsPassedToMethodToName() {
		$expected = uniqid('foo');
		$actual = \TYPO3\CMS\Media\Utility\MediaType::toName($expected);
		$this->assertSame($expected, $actual);
	}

	/**
	 * @test
	 */
	public function mediaTypeIntegerReturnsACorrespondingName() {
		foreach ($this->values as $key => $value) {
			$actual = \TYPO3\CMS\Media\Utility\MediaType::toName($key);
			$this->assertEquals($value, $actual);
		}
	}

	/**
	 * @test
	 */
	public function mediaTypeNameReturnsACorrespondingValue() {
		foreach ($this->values as $key => $value) {
			$actual = \TYPO3\CMS\Media\Utility\MediaType::toInteger($value);
			$this->assertEquals($key, $actual);
		}
	}

	/**
	 * @test
	 */
	public function getTypesReturnsAnArrayAndCheckWhetherItIsNotEmpty() {
		$actual = \TYPO3\CMS\Media\Utility\MediaType::getTypes();
		$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function getTypesReturnsAnArrayAndCheckItHasTwoElementByDefault() {
		$actual = \TYPO3\CMS\Media\Utility\MediaType::getTypes();
		$this->assertTrue(count($actual) == 2);
	}
}
?>

