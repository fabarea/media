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
 * Test case for class \TYPO3\CMS\Media\FileUpload\UploadManager.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class UploadManagerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\FileUpload\UploadManager
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $fakeName = '';

	/**
	 * @var string
	 */
	private $fakePrefix = '';

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\FileUpload\UploadManager();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @dataProvider propertyProvider
	 */
	public function testProperty($propertyName, $value) {
		$setter = 'set' . ucfirst($propertyName);
		$getter = 'get' . ucfirst($propertyName);
		call_user_func_array(array($this->fixture, $setter), array($value));
		$this->assertEquals($value, call_user_func(array($this->fixture, $getter)));
	}

	/**
	 * Provider
	 */
	public function propertyProvider() {
		return array(
			array('uploadFolder', uniqid()),
			array('inputName', uniqid()),
			array('sizeLimit', rand(10,100)),
		);
	}

	/**
	 * @test
	 * @dataProvider fileNameProvider
	 */
	public function sanitizeFileNameTest($actual, $expected) {
		$this->assertSame($expected, $this->fixture->sanitizeFileName($actual));
	}

	/**
	 * Provider
	 */
	public function fileNameProvider() {
		return array(
			array('éléphant', 'elephant'),
			array('foo bar', 'foo-bar'),
			array('Foo Bar', 'Foo-Bar'),
			array('foo_bar', 'foo_bar'),
			array('foo&bar', 'foo-bar'),
			array('foo!bar', 'foo-bar'),
			array('foo~bar', 'foo-bar'),
		);
	}

}
?>