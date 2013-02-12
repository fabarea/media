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
 * Test case for class \TYPO3\CMS\Media\MediaFactory.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaFactoryTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \TYPO3\CMS\Media\MediaFactory
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\MediaFactory();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @dataProvider classNameProvider
	 */
	public function testProperty($className, $values) {
		$object = $this->fixture->createObject($values, $className);
		$this->assertTrue($object instanceof $className);
		$this->assertSame($values['uid'], $object->getUid());
	}

	/**
	 * Provider
	 */
	public function classNameProvider() {
		return array(
			array('TYPO3\CMS\Media\Domain\Model\Media', array('uid' => rand(100, 200))),
			array('TYPO3\CMS\Media\Domain\Model\Text', array('uid' => rand(100, 200))),
			array('TYPO3\CMS\Media\Domain\Model\Image', array('uid' => rand(100, 200))),
			array('TYPO3\CMS\Media\Domain\Model\Audio', array('uid' => rand(100, 200))),
			array('TYPO3\CMS\Media\Domain\Model\Video', array('uid' => rand(100, 200))),
			array('TYPO3\CMS\Media\Domain\Model\Application', array('uid' => rand(100, 200))),
		);
	}
}
?>