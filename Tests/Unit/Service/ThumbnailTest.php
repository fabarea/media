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
 * Test case for class \TYPO3\CMS\Media\Service\Thumbnail.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class ThumbnailTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Service\Thumbnail
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Service\Thumbnail();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function defaultPropertyWrapIsFalse() {
		$this->assertAttributeEquals(FALSE, 'wrap', $this->fixture);
	}

	/**
	 * @test
	 */
	public function isWrappedReturnsFalseByDefault() {
		$this->assertFalse($this->fixture->isWrapped());
	}

	/**
	 * @test
	 */
	public function callWrapMethodAndIsWrappedReturnsTrue() {
		$this->assertTrue($this->fixture->doWrap()->isWrapped());
	}

	/**
	 * @test
	 */
	public function callDoNotWrapMethodAndIsWrappedReturnsTrue() {
		$this->assertFalse($this->fixture->doWrap(FALSE)->isWrapped());
	}

	/**
	 * @test
	 */
	public function isThumbnailPossibleForExtensionPng() {
		$this->assertTrue($this->fixture->isThumbnailPossible('png'));
	}

	/**
	 * @test
	 */
	public function isThumbnailPossibleForExtensionFoo() {
		$this->assertFalse($this->fixture->isThumbnailPossible(uniqid('foo')));
	}

	/**
	 * @test
	 */
	public function testGetterAndSetterForPropertyMedia() {
		$this->fixture->setFile($this->getMockMedia());
		$this->assertTrue($this->fixture->getFile() instanceof \TYPO3\CMS\Media\Domain\Model\Asset);
	}

	/**
	 * @test
	 */
	public function setterForPropertyMediaReturnsInstanceOfSelfObject() {
		$this->assertTrue($this->fixture->setFile($this->getMockMedia()) instanceof \TYPO3\CMS\Media\Service\Thumbnail);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\MissingTcaConfigurationException
	 */
	public function callingMethodCreateWithoutMediaRaisesAnException() {
		$this->fixture->create();
	}

	/**
	 * @return TYPO3\CMS\Media\Domain\Model\Asset
	 */
	private function getMockMedia() {
		$data = array(
			'uid' => rand(10000, 100000),
			'identifier' => uniqid('/foo'),
		);
		return new \TYPO3\CMS\Media\Domain\Model\Asset($data);
	}

	/**
	 * @test
	 */
	public function setRandomAttributesAndControllTheOutput() {
		$value = uniqid('foo');
		$this->fixture->setAttributes(array('foo' => $value));
		$expected = sprintf('foo="%s" ', $value);

		$this->assertEquals($expected, $this->fixture->renderAttributes());
	}
}
?>