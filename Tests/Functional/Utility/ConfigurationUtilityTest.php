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

use Fab\Media\Tests\Functional\AbstractFunctionalTestCase;

require_once dirname(dirname(__FILE__)) . '/AbstractFunctionalTestCase.php';

/**
 * Test case for class \Fab\Media\Utility\Configuration.
 */
class ConfigurationUtilityTest extends AbstractFunctionalTestCase {

	/**
	 * @var ConfigurationUtility
	 */
	private $fixture;

	public function setUp() {
		parent::setUp();
		$this->fixture = new ConfigurationUtility();
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
