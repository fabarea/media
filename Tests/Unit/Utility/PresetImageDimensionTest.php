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
 * Test case for class \TYPO3\CMS\Media\Utility\PresetImageDimension.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class PresetImageDimensionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Utility\PresetImageDimension
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Utility\PresetImageDimension();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\EmptyValueException
	 */
	public function randomPresetShouldReturnException() {
		$this->fixture->preset(uniqid());
	}

	/**
	 * @test
	 */
	public function methodPresetReturnInstanceOfPresetImageDimension() {
		$actual = 'image_thumbnail';
		$object = $this->fixture->preset($actual);
		$this->assertTrue($object instanceof \TYPO3\CMS\Media\Utility\PresetImageDimension);
	}

	/**
	 * @test
	 */
	public function propertyIsEmptyByDefault() {
		$this->assertEmpty($this->fixture->getStore());
	}

	/**
	 * @test
	 */
	public function setImageThumbnailAsPresetAndCheckTheStoreContainsIt() {
		$actual = 'image_thumbnail';
		$this->fixture->preset($actual);
		$this->assertArrayHasKey($actual, $this->fixture->getStore());
	}

	/**
	 * @test
	 */
	public function setImageThumbnailAsPresetAndCheckWidthEquals100() {
		$actual = 'image_thumbnail';
		$this->assertSame(100, $this->fixture->preset($actual)->getWidth());
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 */
	public function getWidthWithoutPresetRaisesAnException() {
		$this->fixture->getWidth();
	}

	/**
	 * @test
	 */
	public function setImageThumbnailAsPresetAndCheckHeightEquals100() {
		$actual = 'image_thumbnail';
		$this->assertSame(100, $this->fixture->preset($actual)->getHeight());
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 */
	public function getHeightWithoutPresetRaisesAnException() {
		$this->fixture->getHeight();
	}
}
?>