<?php
namespace Fab\Media\Utility;

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
?>