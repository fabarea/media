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

require_once dirname(dirname(__FILE__)) . '/AbstractFunctionalTestCase.php';

/**
 * Test case for class \Fab\Media\Utility\Configuration.
 */
class ConfigurationUtilityTest extends \Fab\Media\Tests\Functional\AbstractFunctionalTestCase {

	/**
	 * @var \Fab\Media\Utility\ConfigurationUtility
	 */
	private $fixture;

	public function setUp() {
		parent::setUp();
		$this->fixture = new \Fab\Media\Utility\ConfigurationUtility();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getConfigurationReturnNotEmptyArrayByDefault() {
		$actual = $this->fixture->getConfiguration();
		$this->assertTrue(is_array($actual));
		$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function thumbnailSizeSettingReturnsNotEmpty() {
		$actual = $this->fixture->get('image_thumbnail');
		$this->assertTrue($actual > 1);
	}

	/**
	 * @test
	 */
	public function getFooValueReturnsEmpty() {
		$expected = '';
		$actual = $this->fixture->get(uniqid('foo'));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @test
	 */
	public function configurationArrayNotEmptyAfterGetARandomValue() {
		$this->fixture->get(uniqid('foo'));

		$actual = $this->fixture->getConfiguration();
		$this->assertTrue(count($actual) > 0);
	}

	/**
	 * @test
	 */
	public function setConfigurationValueAndCheckReturnedValueIsCorresponding() {
		$expected = 'bar';
		$this->fixture->set('foo', $expected);
		$this->assertSame($expected, $this->fixture->get('foo'));
	}
}
?>