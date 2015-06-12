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
 * Test case for class \Fab\Media\Utility\PermissionUtility.
 */
class PermissionUtilityTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Fab\Media\Utility\PermissionUtility
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \Fab\Media\Utility\PermissionUtility();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function checkWhetherPermissionUtilityIsCorrect() {
		$this->assertInstanceOf('\Fab\Media\Utility\PermissionUtility', $this->fixture);
	}
}