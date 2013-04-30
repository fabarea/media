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
 * Test case for class \TYPO3\CMS\Media\Utility\SettingImagePreset.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class SettingImagePresetTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Utility\SettingImagePreset
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Utility\SettingImagePreset();
	}

	public function tearDown() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::purgeInstances();
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
	public function methodPresetReturnInstanceOfSettingImagePreset() {
		$actual = 'image_thumbnail';
		$object = $this->fixture->preset($actual);
		$this->assertTrue($object instanceof \TYPO3\CMS\Media\Utility\SettingImagePreset);
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
	 * @dataProvider presetProvider
	 */
	public function testProperty($preset, $setting, $width, $height) {
		\TYPO3\CMS\Media\Utility\Setting::getInstance()->set($preset, $setting);
		$this->assertSame($width, $this->fixture->preset($preset)->getWidth());
		$this->assertSame($height, $this->fixture->preset($preset)->getHeight());
	}

	/**
	 * Provider
	 */
	public function presetProvider() {
		return array(
			array('image_thumbnail', '110x100', 110, 100),
			array('image_mini', '130x120', 130, 120),
			array('image_small', '340x320', 340, 320),
			array('image_medium', '780x760', 780, 760),
			array('image_large', '1210x1200', 1210, 1200),
			array('image_original', '1910x1920', 1910, 1920),
		);
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
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 */
	public function getHeightWithoutPresetRaisesAnException() {
		$this->fixture->getHeight();
	}

	/**
	 * @test
	 */
	public function setOriginalImageAsPresetWithValue0AndCheckWidthEquals0() {
		$actual = 'image_original';
		\TYPO3\CMS\Media\Utility\Setting::getInstance()->set('image_original', 0);
		$this->assertSame(0, $this->fixture->preset($actual)->getWidth());
	}

	/**
	 * @test
	 */
	public function setOriginalImageAsPresetWithRandomValueAndCheckWidthAndHeightCorrespondsToThisRandomValue() {
		$preset = 'image_original';
		$actualWidth = rand(10, 100);
		$actualHeight = rand(10, 100);
		\TYPO3\CMS\Media\Utility\Setting::getInstance()->set('image_original', $actualWidth . 'x' . $actualHeight);
		$this->assertSame($actualWidth, $this->fixture->preset($preset)->getWidth());
		$this->assertSame($actualHeight, $this->fixture->preset($preset)->getHeight());
	}
}
?>