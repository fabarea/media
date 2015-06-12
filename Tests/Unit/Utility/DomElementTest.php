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

/**
 * Test case for class \Fab\Media\Utility\DomElement.
 */
class DomElementTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Fab\Media\Utility\DomElement
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \Fab\Media\Utility\DomElement();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @dataProvider nameProvider
	 */
	public function givenNameCanBeConvertedToAnExpectedId($input, $expectedId) {
		$this->assertEquals($expectedId, $this->fixture->formatId($input));
	}

	/**
	 * Provider
	 */
	public function nameProvider() {
		return array(
			array('foobar', 'foobar'),
			array('foo-bar', 'foo-bar'),
			array('foo_bar', 'foo-bar'),
			array('FooBarBaz', 'foo-bar-baz'),
			array('Foo Bar', 'foo-bar'),
			array('foo[bar]', 'foo-bar'),
		);
	}
}