<?php
namespace Fab\Media\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Tests\Functional\AbstractFunctionalTestCase;

require_once dirname(dirname(__FILE__)) . '/AbstractFunctionalTestCase.php';


/**
 * Test case for class \Fab\Media\Utility\ImagePresetUtility.
 */
class ImagePresetUtilityTest extends AbstractFunctionalTestCase {

	/**
	 * @var ImagePresetUtility
	 */
	private $fixture;

	public function setUp() {
		parent::setUp();
		$this->fixture = new ImagePresetUtility();
	}

	public function tearDown() {
		GeneralUtility::purgeInstances();
		unset($this->fixture);
	}

	/**
	 * @test
	 * @expectedException \Fab\Media\Exception\EmptyValueException
	 */
	public function randomPresetShouldReturnException() {
		$this->fixture->preset(uniqid());
	}

	/**
	 * @test
	 */
	public function methodPresetReturnInstanceOfImagePresetUtility() {
		$actual = 'image_thumbnail';
		$object = $this->fixture->preset($actual);
		$this->assertTrue($object instanceof ImagePresetUtility);
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
		ConfigurationUtility::getInstance()->set($preset, $setting);
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
		);
	}

	/**
	 * @test
	 * @expectedException \Fab\Media\Exception\InvalidKeyInArrayException
	 */
	public function getWidthWithoutPresetRaisesAnException() {
		$this->fixture->getWidth();
	}

	/**
	 * @test
	 * @expectedException \Fab\Media\Exception\InvalidKeyInArrayException
	 */
	public function getHeightWithoutPresetRaisesAnException() {
		$this->fixture->getHeight();
	}

	/**
	 * @test
	 */
	public function setOriginalImageAsPresetWithValue0AndCheckWidthEquals0() {
		$actual = 'image_large';
		ConfigurationUtility::getInstance()->set('image_large', 0);
		$this->assertSame(0, $this->fixture->preset($actual)->getWidth());
	}

	/**
	 * @test
	 */
	public function setOriginalImageAsPresetWithRandomValueAndCheckWidthAndHeightCorrespondsToThisRandomValue() {
		$preset = 'image_large';
		$actualWidth = rand(10, 100);
		$actualHeight = rand(10, 100);
		ConfigurationUtility::getInstance()->set('image_large', $actualWidth . 'x' . $actualHeight);
		$this->assertSame($actualWidth, $this->fixture->preset($preset)->getWidth());
		$this->assertSame($actualHeight, $this->fixture->preset($preset)->getHeight());
	}

}