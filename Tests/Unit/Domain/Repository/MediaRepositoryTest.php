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
 * Test case for class \TYPO3\CMS\Media\Domain\Repository\MediaRepository.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaRepositoryTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var Tx_Phpunit_Framework
	 */
	private $testingFramework;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\MediaRepository
	 */
	protected $fixture;

	public function setUp() {
		$this->testingFramework = new Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Repository\MediaRepository();
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\MissingUidException
	 */
	public function updateMediaReturnsException() {
		$this->fixture->updateMedia(array());
	}
}
?>