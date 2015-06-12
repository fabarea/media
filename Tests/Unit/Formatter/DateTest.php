<?php
namespace Fab\Media\Formatter;

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
 * Test case for class \Fab\Media\Formatter\Date.
 */
class DateTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Fab\Media\Formatter\Date
	 */
	private $fixture;

	public function setUp() {
		date_default_timezone_set('GMT');
		$this->fixture = new \Fab\Media\Formatter\Date();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function canFormatDate() {
		$foo = $this->fixture->format('1351880525');
		$this->assertEquals('02.11.2012', $foo);

	}
}